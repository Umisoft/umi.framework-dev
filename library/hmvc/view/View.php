<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\view;

use ArrayIterator;
use umi\hmvc\controller\IController;
use umi\hmvc\dispatcher\IDispatchContext;
use umi\hmvc\exception\ViewRenderException;
use umi\hmvc\widget\IWidget;
use umi\spl\container\TArrayAccess;
use umi\spl\container\TPropertyAccess;

/**
 * Содержимое результата работы виджета или контроллера, требующее шаблонизации.
 */
class View extends ArrayIterator implements IView
{
    use TArrayAccess;
    use TPropertyAccess;

    /**
     * @var IController|IWidget $viewOwner
     */
    protected $viewOwner;
    /**
     * @var IDispatchContext $context контекст вызова виджета
     */
    protected $context;
    /**
     * @var string $templateName имя шаблона
     */
    protected $templateName;
    /**
     * @var array $variables переменные
     */
    protected $variables = [];
    /**
     * @var string $renderedResult результат рендеринга View
     */
    protected $renderedResult;

    /**
     * Конструктор.
     * @param IController|IWidget $viewOwner
     * @param IDispatchContext $context контекст вызова виджета
     * @param string $templateName имя шаблона
     * @param array $variables переменные шаблона
     */
    public function __construct($viewOwner, IDispatchContext $context, $templateName, array $variables = [])
    {
        $this->viewOwner = $viewOwner;
        $this->context = $context;
        $this->templateName = $templateName;
        $this->variables = $variables;

        parent::__construct($variables);
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        if (is_string($this->renderedResult)) {
            return $this->renderedResult;
        }

        $dispatcher = $this->context->getDispatcher();
        $previousContext = $dispatcher->switchCurrentContext($this->context);

        try {
            $result = $this->context->getComponent()->getViewRenderer()->render($this->templateName, $this->variables);

            if ($previousContext) {
                $dispatcher->switchCurrentContext($previousContext);
            }

            return $result;
        } catch (\Exception $e) {
            $exception = new ViewRenderException(
                sprintf('Cannot render template "%s".', $this->templateName),
                0,
                $e
            );

            try {
                $result = $dispatcher->reportViewRenderError($exception, $this->context, $this->viewOwner);

                if ($previousContext) {
                    $dispatcher->switchCurrentContext($previousContext);
                }
            } catch (\Exception $e) {
                $result = $e->getMessage();
            }

            return $result;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize((string) $this);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        $this->renderedResult = unserialize($serialized);
    }

    /**
     * {@inheritdoc}
     */
    public function get($attribute)
    {
        return $this->has($attribute) ? $this->variables[$attribute] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function set($attribute, $value)
    {
        $this->variables[$attribute] = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function has($attribute)
    {
        return isset($this->variables[$attribute]);
    }

    /**
     * {@inheritdoc}
     */
    public function del($attribute)
    {
        unset($this->variables[$attribute]);

        return $this;
    }
}
 