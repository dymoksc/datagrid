<?php

namespace Jfsimon\Datagrid\Infra\Handler;

use Jfsimon\Datagrid\Infra\Extension\LabelExtension;
use Jfsimon\Datagrid\Infra\Formatter\LabelFormatter;
use Jfsimon\Datagrid\Model\Column;
use Jfsimon\Datagrid\Model\Component\Cell;
use Jfsimon\Datagrid\Model\Component\Label;
use Jfsimon\Datagrid\Model\Data\Entity;
use Jfsimon\Datagrid\Model\Trans;
use Jfsimon\Datagrid\Service\HandlerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Jean-François Simon <contact@jfsimon.fr>
 */
class LabelHandler implements HandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function configure(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            LabelExtension::NAME              => true,
            LabelExtension::NAME.'_trans'     => Trans::disable(),
            LabelExtension::NAME.'_formatter' => true,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Column $column, Entity $entity = null, array $options = array())
    {
        $subject = is_string($options[LabelExtension::NAME]) ? $options[LabelExtension::NAME] : $column->getName();
        $label = $this->getLabel(
            $subject,
            $column->getGrid()->getName(),
            $options[LabelExtension::NAME.'_trans'],
            $options[LabelExtension::NAME.'_formatter']
        );
        return new Cell($label);
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return LabelExtension::NAME;
    }

    /**
     * @param string $subject
     * @param string $grid
     * @param Trans $trans
     * @param bool $useFormatter
     *
     * @return Label
     */
    private function getLabel($subject, $grid, Trans $trans, $useFormatter)
    {
        if ($trans->isEnabled()) {
            $label = $trans->resolvePattern(array(
                '{grid}'      => $grid,
                '{extension}' => LabelExtension::NAME,
                '{subject}'   => $subject,
            ));

            return new Label($label, true, $trans->getDomain());
        }

        $formatter = new LabelFormatter();

        return new Label($useFormatter ? $formatter->format($subject) : $subject);
    }
}
