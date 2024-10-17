<?php
namespace App\Helpers;

use Illuminate\Support\HtmlString;

class OlistHelper
{
    /**
     * Render form data as an HTML unordered list.
     *
     * @param array $formData
     * @return string
     */
    public static function renderFormDataAsList(array $formData): string
    {
        if (empty($formData)) {
            return '<p>No data available</p>';  // Handle the case when form_data is empty
        }

        $listItems = '';
        foreach ($formData as $key => $value) {
            $listItems .= '<li><strong>' . ucfirst(str_replace('_', ' ', $key)) . ':</strong> ' . htmlspecialchars($value) . '</li>';
        }

        return new HtmlString('<ul>' . $listItems . '</ul>');
    }
}
