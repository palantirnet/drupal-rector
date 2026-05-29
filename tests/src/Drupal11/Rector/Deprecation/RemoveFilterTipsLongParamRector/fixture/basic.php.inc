<?php

// Class extending FilterBase — $long parameter removed.
class MyHtmlFilter extends FilterBase
{
    public function tips($long = FALSE)
    {
        return $this->t('No HTML tags allowed.');
    }
}

// Class implementing FilterInterface — $long parameter removed.
class MyEscapeFilter implements FilterInterface
{
    public function tips($long = FALSE)
    {
        return $this->t('HTML is escaped.');
    }
}

// Class extending FilterBase where $long IS used — must not change.
class MyLongTipsFilter extends FilterBase
{
    public function tips($long = FALSE)
    {
        if ($long) {
            return $this->t('Long tip with details.');
        }

        return $this->t('Short tip.');
    }
}

// Unrelated class — must not change.
class MyUnrelatedClass
{
    public function tips($long = FALSE)
    {
        return 'something';
    }
}

// _filter_tips() call with second argument — second argument removed.
_filter_tips(0, FALSE);

// _filter_tips() call without second argument — must not change.
_filter_tips(0);

?>
-----
<?php

// Class extending FilterBase — $long parameter removed.
class MyHtmlFilter extends FilterBase
{
    public function tips()
    {
        return $this->t('No HTML tags allowed.');
    }
}

// Class implementing FilterInterface — $long parameter removed.
class MyEscapeFilter implements FilterInterface
{
    public function tips()
    {
        return $this->t('HTML is escaped.');
    }
}

// Class extending FilterBase where $long IS used — must not change.
class MyLongTipsFilter extends FilterBase
{
    public function tips($long = FALSE)
    {
        if ($long) {
            return $this->t('Long tip with details.');
        }

        return $this->t('Short tip.');
    }
}

// Unrelated class — must not change.
class MyUnrelatedClass
{
    public function tips($long = FALSE)
    {
        return 'something';
    }
}

// _filter_tips() call with second argument — second argument removed.
_filter_tips(0);

// _filter_tips() call without second argument — must not change.
_filter_tips(0);

?>
