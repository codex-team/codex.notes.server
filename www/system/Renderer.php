<?php

namespace App\System;

use App\Components\Base\Models\Exceptions\RenderException;

/**
 * Class Renderer
 * Provides template rendering methods
 *
 * @package App\System
 */
class Renderer
{
    /**
     * Return template with passed data
     *
     * @param string      $tpl      - path to the template related to the current component\s tpl/ folder
     * @param array|null  $viewData - data passed to the template
     * @param string|null $layout   – layout filename
     *
     * @return string
     */
    public static function render(string $tpl, array $viewData = null, $layout = 'layout.php'): string
    {
        /**
         * Get caller classname
         */
        $callerClass = debug_backtrace()[1]['class'];

        /**
         * Extract component name from string like 'App\Components\Index\Index'
         */
        $componentPath = explode('\\', $callerClass);
        $componentIndex = array_search(Config::DIR_COMPONENTS, array_map('strtolower', $componentPath));
        if ($componentIndex === false) {
            throw new RenderException('Components directory not found in use string');
        }
        if ($componentIndex + 1 > count($componentPath)) {
            throw new RenderException('Component index doesn\'t exist in use string');
        }
        $componentName = strtolower($componentPath[$componentIndex + 1]);

        /**
         * Compose template path
         */
        $tplDir = self::getTemplateDirectory($componentName);
        $tplPath = $tplDir . $tpl;
        /**
         * Render template content
         */
        $templateContent = self::renderTemplate($tplPath, $viewData);

        /**
         * Render layout with the template content or not
         */
        if (isset($layout)) {
            $layoutData = array_merge($viewData, ['content' => $templateContent]);

            return self::renderTemplate(self::getTemplateDirectory('base') . $layout, $layoutData);
        } else {
            return $templateContent;
        }
    }

    /**
     * Returns template forder path from the component
     *
     * @param string $componentName - component name
     *
     * @return string
     */
    private static function getTemplateDirectory(string $componentName): string
    {
        /**
         * Path to the /components directory
         */
        $componensDir = Config::DIR_COMPONENTS;

        /**
         * Path to the /tpl directory inside the component
         */
        return $componensDir . DIRECTORY_SEPARATOR . $componentName . DIRECTORY_SEPARATOR . 'tpl' . DIRECTORY_SEPARATOR;
    }

    /**
     * Renders template with data
     *
     * @param string $file
     * @param array  $vars - variables for the template
     *
     * @return string
     */
    private static function renderTemplate($file, array $vars = null): string
    {
        if (is_array($vars) && !empty($vars)) {
            extract($vars);
        }

        /**
         * Enable output bufferisation
         */
        ob_start();

        include PROJECTROOT . $file;

        return ob_get_clean();
    }
}
