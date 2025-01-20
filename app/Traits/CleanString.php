<?php
/*
Dev Omar Shaheen
Devomar095@gmail.com
WhatsApp +972592554320
*/

namespace App\Traits;

trait CleanString
{
    /**
     * The attributes that should be cleaned.
     *
     * @var array
     */
    protected $cleanAttributes = ['name'];

    /**
     * Initialize the trait.
     *
     * @return void
     */
    public function initializeCleanString()
    {
        $this->cleanAttributes = ['name'];
    }

    /**
     * Boot the trait.
     *
     * @return void
     */
    protected static function bootCleanString()
    {
        static::saving(function ($model) {
            $model->cleanStrings();
        });
    }

    /**
     * Clean the specified attributes.
     *
     * @return void
     */
    protected function cleanStrings()
    {
        foreach ($this->getCleanAttributes() as $attribute) {
            if (isset($this->attributes[$attribute])) {
                $this->attributes[$attribute] = $this->cleanStringValue($this->attributes[$attribute]);
            }
        }
    }

    /**
     * Clean a string value by removing NBSP, duplicate spaces and trimming.
     *
     * @param string $value
     * @return string
     */
    protected function cleanStringValue($value)
    {
        // Convert NBSP to regular space
        $value = str_replace("\xC2\xA0", ' ', $value);

        // Remove duplicate spaces
        $value = preg_replace('/\s+/', ' ', $value);

        // Trim spaces from start and end
        return trim($value);
    }

    /**
     * Get the attributes that should be cleaned.
     *
     * @return array
     */
    public function getCleanAttributes()
    {
        return $this->cleanAttributes;
    }

    /**
     * Set the attributes that should be cleaned.
     *
     * @param array|string $attributes
     * @return $this
     */
    public function cleanAttributes($attributes)
    {
        $this->cleanAttributes = is_array($attributes) ? $attributes : func_get_args();

        return $this;
    }
}
