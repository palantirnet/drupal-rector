<?php

namespace Drupal\acquia_contenthub\Commands;

use Acquia\ContentHubClient\ContentHubClient;
use Drupal\acquia_contenthub\AcquiaContentHubEvents;
use Drupal\acquia_contenthub\Client\ClientFactory;
use Drupal\acquia_contenthub\ContentHubConnectionManager;
use Drupal\acquia_contenthub\Event\AcquiaContentHubUnregisterEvent;
use Drupal\acquia_contenthub\Form\ContentHubSettingsForm;
use Drupal\acquia_contenthub\Settings\ContentHubConfigurationInterface;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Form\FormState;
use Drush\Commands\DrushCommands;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Drush commands for interacting with Acquia Content Hub client site.
 *
 * @package Drupal\acquia_contenthub\Commands
 */
class AcquiaContentHubSiteCommands extends DrushCommands {

    /**
     * The client factory.
     *
     * @var \Drupal\acquia_contenthub\Client\ClientFactory
     */
    protected $clientFactory;

    /**
     * Event dispatcher.
     *
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * ACH connection manager.
     *
     * @var \Drupal\acquia_contenthub\ContentHubConnectionManager
     */
    protected $achConnectionManager;

    /**
     * CH configurations.
     *
     * @var \Drupal\acquia_contenthub\Settings\ContentHubConfigurationInterface
     */
    protected ContentHubConfigurationInterface $achConfigurations;

    /**
     * The form builder.
     *
     * @var \Drupal\Core\Form\FormBuilderInterface
     */
    protected FormBuilderInterface $formBuilder;

    /**
     * AcquiaContentHubSiteCommands constructor.
     *
     * @param \Drupal\acquia_contenthub\Client\ClientFactory $client_factory
     *   ACH client factory.
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     *   Symfony event dispatcher.
     * @param \Drupal\acquia_contenthub\ContentHubConnectionManager $achConnectionManager
     *   ACH connection manager.
     * @param \Drupal\acquia_contenthub\Settings\ContentHubConfigurationInterface $ach_configuration
     *   CH configurations.
     * @param \Drupal\Core\Form\FormBuilderInterface $form_builder
     *   Form builder..
     */
    public function __construct(ClientFactory $client_factory, EventDispatcherInterface $eventDispatcher, ContentHubConnectionManager $achConnectionManager, ContentHubConfigurationInterface $ach_configuration, FormBuilderInterface $form_builder) {
        $this->clientFactory = $client_factory;
        $this->eventDispatcher = $eventDispatcher;
        $this->achConnectionManager = $achConnectionManager;
        $this->achConfigurations = $ach_configuration;
        $this->formBuilder = $form_builder;
    }

    /**
     * Connects a site with contenthub.
     *
     * @command acquia:contenthub-connect-site
     * @aliases ach-connect,acquia-contenthub-connect-site
     *
     * @option $hostname
     *   Content Hub API URL.
     * @default $hostname null
     *
     * @option $api_key
     *   Content Hub API Key.
     * @default $api_key null
     *
     * @option $secret_key
     *   Content Hub API Secret.
     * @default $secret_key null
     *
     * @option $client_name
     *   The client name for this site.
     * @default $client_name null
     *
     * @usage ach-connect
     *   hostname, api_key, secret_key, client_name will be requested.
     * @usage ach-connect --hostname=https://us-east-1.content-hub.acquia.com
     *   api_key, secret_key, client_name will be requested.
     * @usage ach-connect --hostname=https://us-east-1.content-hub.acquia.com --api_key=API_KEY
     *   --secret_key=SECRET_KEY --client_name=CLIENT_NAME Connects site with
     *   following credentials.
     */
    public function contenthubConnectSite() {
        $options = $this->input()->getOptions();

        // @todo Revisit initial connection logic with our event subscibers.
        $settings = $this->achConfigurations->getSettings();
        $config_origin = $settings->getUuid();

        $provider = $this->achConfigurations->getProvider();
        if ($provider != 'core_config') {
            $message = dt('Settings are being provided by @provider, and already connected.', ['@provider' => $provider]);
            $this->logger()->warning($message);
            return;
        }

        if (!empty($config_origin)) {
            $message = dt('Site is already connected to Content Hub. Skipping.');
            $this->logger()->warning($message);
            return;
        }

        $io = $this->io();
        $hostname = $options['hostname'] ?? $io->ask(
            dt('What is the Content Hub API URL?'),
            'https://us-east-1.content-hub.acquia.com'
        );
        $api_key = $options['api_key'] ?? $io->ask(
            dt('What is your Content Hub API Key?')
        );
        $secret_key = $options['secret_key'] ?? $io->ask(
            dt('What is your Content Hub API Secret?')
        );

        $client_name = $options['client_name'] ?? $io->ask(
            dt('What is the client name for this site?'),
        );

        $form_state = (new FormState())->setValues([
            'hostname' => $hostname,
            'api_key' => $api_key,
            'secret_key' => $secret_key,
            'client_name' => $client_name,
            'op' => 'Save configuration',
        ]);

        // @todo Errors handling can be improved after relocation of registration
        // logic into separate service.
        $new_form_state = new FormState();
        $form = $this->formBuilder->buildForm(ContentHubSettingsForm::class, $new_form_state);
        $form_state->setTriggeringElement($form['actions']['submit']);
        $this->formBuilder->submitForm(ContentHubSettingsForm::class, $form_state);
    }

    /**
     * Disconnects a site with contenthub.
     *
     * @option delete
     *   Flag to delete all the entities from Content Hub.
     * @default delete
     *
     * @command acquia:contenthub-disconnect-site
     * @aliases ach-disconnect,acquia-contenthub-disconnect-site
     */
    public function contenthubDisconnectSite() {
        $client = $this->clientFactory->getClient();

        if (!$client instanceof ContentHubClient) {
            $message = "Couldn't instantiate client. Please check connection settings.";
            $this->logger->warning($message);
            return;
        }

        $provider = $this->achConfigurations->getProvider();
        if ($provider != 'core_config') {
            $message = dt(
                'Settings are being provided by %provider and cannot be disconnected manually.',
                ['%provider' => $provider]
            );
            $this->logger->warning($message);
            return;
        }

        $client = $this->clientFactory->getClient();
        $settings = $client->getSettings();
        $remote_settings = $client->getRemoteSettings();

        foreach ($remote_settings['webhooks'] as $webhook) {
            // Checks that webhook from settings and url from options are matching.
            $uri_option = $this->input->getOption('uri');
            if ($uri_option && $settings->getWebhook() !== $uri_option) {
                continue;
            }

            if ($webhook['client_name'] === $settings->getName()) {
                $webhook_uuid = $webhook['uuid'];
                break;
            }
        }

        if (empty($webhook_uuid)) {
            $this->logger->error('Cannot find webhook UUID.');
            return;
        }

        $event = new AcquiaContentHubUnregisterEvent($webhook_uuid);
        $this->eventDispatcher->dispatch($event, AcquiaContentHubEvents::ACH_UNREGISTER);

        try {
            $delete = $this->input->getOption('delete');
            if ($delete === 'all') {
                $warning_message = dt('This command will delete ALL the entities published by this origin before unregistering the client. There is no way back from this action. It might take a while depending how many entities originated from this site. Are you sure you want to proceed (Y/n)?');
                if ($this->io()->confirm($warning_message) === FALSE) {
                    $this->logger->error('Cancelled.');
                    return;
                }
                foreach ($event->getOrphanedEntities() as $entity) {
                    if ($entity['type'] === 'client') {
                        continue;
                    }
                    $client->deleteEntity($entity['uuid']);
                }
            }

            if ($event->getOrphanedEntitiesAmount() > 0 && $delete !== 'all') {
                $message = sprintf('There are %s entities published from this origin. You have to delete/reoriginate those entities before proceeding with the unregistration. If you want to delete those entities and unregister the client, use the following drush command "drush ach-disconnect --delete=all".',
                    $event->getOrphanedEntitiesAmount()
                );
                $this->logger->warning($message);
                return;
            }

            $this->achConnectionManager->unregister($event);
        }
        catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
        }

        $client_name = $this->achConfigurations->getConnectionDetails()->getClientName();
        $this->achConfigurations->getContentHubConfig()->invalidateContentHubSettings();

        $message = dt(
            'Successfully disconnected site %site from contenthub',
            ['%site' => $client_name]
        );
        $this->logger->notice($message);
    }

}
?>
-----
<?php

namespace Drupal\acquia_contenthub\Commands;

use Acquia\ContentHubClient\ContentHubClient;
use Drupal\acquia_contenthub\AcquiaContentHubEvents;
use Drupal\acquia_contenthub\Client\ClientFactory;
use Drupal\acquia_contenthub\ContentHubConnectionManager;
use Drupal\acquia_contenthub\Event\AcquiaContentHubUnregisterEvent;
use Drupal\acquia_contenthub\Form\ContentHubSettingsForm;
use Drupal\acquia_contenthub\Settings\ContentHubConfigurationInterface;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Form\FormState;
use Drush\Commands\DrushCommands;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Drush commands for interacting with Acquia Content Hub client site.
 *
 * @package Drupal\acquia_contenthub\Commands
 */
class AcquiaContentHubSiteCommands extends DrushCommands {

    /**
     * The client factory.
     *
     * @var \Drupal\acquia_contenthub\Client\ClientFactory
     */
    protected $clientFactory;

    /**
     * Event dispatcher.
     *
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * ACH connection manager.
     *
     * @var \Drupal\acquia_contenthub\ContentHubConnectionManager
     */
    protected $achConnectionManager;

    /**
     * CH configurations.
     *
     * @var \Drupal\acquia_contenthub\Settings\ContentHubConfigurationInterface
     */
    protected ContentHubConfigurationInterface $achConfigurations;

    /**
     * The form builder.
     *
     * @var \Drupal\Core\Form\FormBuilderInterface
     */
    protected FormBuilderInterface $formBuilder;

    /**
     * AcquiaContentHubSiteCommands constructor.
     *
     * @param \Drupal\acquia_contenthub\Client\ClientFactory $client_factory
     *   ACH client factory.
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     *   Symfony event dispatcher.
     * @param \Drupal\acquia_contenthub\ContentHubConnectionManager $achConnectionManager
     *   ACH connection manager.
     * @param \Drupal\acquia_contenthub\Settings\ContentHubConfigurationInterface $ach_configuration
     *   CH configurations.
     * @param \Drupal\Core\Form\FormBuilderInterface $form_builder
     *   Form builder..
     */
    public function __construct(ClientFactory $client_factory, EventDispatcherInterface $eventDispatcher, ContentHubConnectionManager $achConnectionManager, ContentHubConfigurationInterface $ach_configuration, FormBuilderInterface $form_builder) {
        $this->clientFactory = $client_factory;
        $this->eventDispatcher = $eventDispatcher;
        $this->achConnectionManager = $achConnectionManager;
        $this->achConfigurations = $ach_configuration;
        $this->formBuilder = $form_builder;
    }

    /**
     * Connects a site with contenthub.
     *
     * @command acquia:contenthub-connect-site
     * @aliases ach-connect,acquia-contenthub-connect-site
     *
     * @option $hostname
     *   Content Hub API URL.
     * @default $hostname null
     *
     * @option $api_key
     *   Content Hub API Key.
     * @default $api_key null
     *
     * @option $secret_key
     *   Content Hub API Secret.
     * @default $secret_key null
     *
     * @option $client_name
     *   The client name for this site.
     * @default $client_name null
     *
     * @usage ach-connect
     *   hostname, api_key, secret_key, client_name will be requested.
     * @usage ach-connect --hostname=https://us-east-1.content-hub.acquia.com
     *   api_key, secret_key, client_name will be requested.
     * @usage ach-connect --hostname=https://us-east-1.content-hub.acquia.com --api_key=API_KEY
     *   --secret_key=SECRET_KEY --client_name=CLIENT_NAME Connects site with
     *   following credentials.
     */
    public function contenthubConnectSite() {
        $options = $this->input()->getOptions();

        // @todo Revisit initial connection logic with our event subscibers.
        $settings = $this->achConfigurations->getSettings();
        $config_origin = $settings->getUuid();

        $provider = $this->achConfigurations->getProvider();
        if ($provider != 'core_config') {
            $message = dt('Settings are being provided by @provider, and already connected.', ['@provider' => $provider]);
            $this->logger()->warning($message);
            return;
        }

        if (!empty($config_origin)) {
            $message = dt('Site is already connected to Content Hub. Skipping.');
            $this->logger()->warning($message);
            return;
        }

        $io = $this->io();
        $hostname = $options['hostname'] ?? $io->ask(
            dt('What is the Content Hub API URL?'),
            'https://us-east-1.content-hub.acquia.com'
        );
        $api_key = $options['api_key'] ?? $io->ask(
            dt('What is your Content Hub API Key?')
        );
        $secret_key = $options['secret_key'] ?? $io->ask(
            dt('What is your Content Hub API Secret?')
        );

        $client_name = $options['client_name'] ?? $io->ask(
            dt('What is the client name for this site?'),
        );

        $form_state = (new FormState())->setValues([
            'hostname' => $hostname,
            'api_key' => $api_key,
            'secret_key' => $secret_key,
            'client_name' => $client_name,
            'op' => 'Save configuration',
        ]);

        // @todo Errors handling can be improved after relocation of registration
        // logic into separate service.
        $new_form_state = new FormState();
        $form = $this->formBuilder->buildForm(ContentHubSettingsForm::class, $new_form_state);
        $form_state->setTriggeringElement($form['actions']['submit']);
        $this->formBuilder->submitForm(ContentHubSettingsForm::class, $form_state);
    }

    /**
     * Disconnects a site with contenthub.
     *
     * @option delete
     *   Flag to delete all the entities from Content Hub.
     * @default delete
     *
     * @command acquia:contenthub-disconnect-site
     * @aliases ach-disconnect,acquia-contenthub-disconnect-site
     */
    public function contenthubDisconnectSite() {
        $client = $this->clientFactory->getClient();

        if (!$client instanceof ContentHubClient) {
            $message = "Couldn't instantiate client. Please check connection settings.";
            $this->logger->warning($message);
            return;
        }

        $provider = $this->achConfigurations->getProvider();
        if ($provider != 'core_config') {
            $message = dt(
                'Settings are being provided by %provider and cannot be disconnected manually.',
                ['%provider' => $provider]
            );
            $this->logger->warning($message);
            return;
        }

        $client = $this->clientFactory->getClient();
        $settings = $client->getSettings();
        $remote_settings = $client->getRemoteSettings();

        foreach ($remote_settings['webhooks'] as $webhook) {
            // Checks that webhook from settings and url from options are matching.
            $uri_option = $this->input->getOption('uri');
            if ($uri_option && $settings->getWebhook() !== $uri_option) {
                continue;
            }

            if ($webhook['client_name'] === $settings->getName()) {
                $webhook_uuid = $webhook['uuid'];
                break;
            }
        }

        if (empty($webhook_uuid)) {
            $this->logger->error('Cannot find webhook UUID.');
            return;
        }

        $event = new AcquiaContentHubUnregisterEvent($webhook_uuid);
        $this->eventDispatcher->dispatch($event, AcquiaContentHubEvents::ACH_UNREGISTER);

        try {
            $delete = $this->input->getOption('delete');
            if ($delete === 'all') {
                $warning_message = dt('This command will delete ALL the entities published by this origin before unregistering the client. There is no way back from this action. It might take a while depending how many entities originated from this site. Are you sure you want to proceed (Y/n)?');
                if ($this->io()->confirm($warning_message) === FALSE) {
                    $this->logger->error('Cancelled.');
                    return;
                }
                foreach ($event->getOrphanedEntities() as $entity) {
                    if ($entity['type'] === 'client') {
                        continue;
                    }
                    $client->deleteEntity($entity['uuid']);
                }
            }

            if ($event->getOrphanedEntitiesAmount() > 0 && $delete !== 'all') {
                $message = sprintf('There are %s entities published from this origin. You have to delete/reoriginate those entities before proceeding with the unregistration. If you want to delete those entities and unregister the client, use the following drush command "drush ach-disconnect --delete=all".',
                    $event->getOrphanedEntitiesAmount()
                );
                $this->logger->warning($message);
                return;
            }

            $this->achConnectionManager->unregister($event);
        }
        catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
        }

        $client_name = $this->achConfigurations->getConnectionDetails()->getClientName();
        $this->achConfigurations->getContentHubConfig()->invalidateContentHubSettings();

        $message = dt(
            'Successfully disconnected site %site from contenthub',
            ['%site' => $client_name]
        );
        $this->logger->notice($message);
    }

}
?>
