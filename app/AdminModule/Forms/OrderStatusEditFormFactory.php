<?php

namespace App\AdminModule\Forms;

use App\Forms\FormFactory;
use App\Model\Facades\OrdersFacade;
use Closure;
use Nette\Forms\Form;
use Nette\Utils\ArrayHash;

class OrderStatusEditFormFactory
{
    private Closure $onSuccess;
    private Closure $onFailure;

    public function __construct(
        private readonly FormFactory $formFactory,
        private readonly OrdersFacade $ordersFacade
    )
    {
    }

    public function create(callable $onSuccess, callable $onFailure): Form
    {
        $form = $this->formFactory->create();

        $form->addHidden('orderId')
            ->setNullable();

        $form->addSelect('orderStatus', 'Stav objednávky', $this->ordersFacade->findOrderStatusPairs());

        $form->addSubmit('submit', 'Změnit stav');

        $form->onSuccess[] = $this->formSucceeded(...);

        $this->onSuccess = $onSuccess;
        $this->onFailure = $onFailure;

        return $form;
    }

    public function formSucceeded(Form $form, ArrayHash $values): void
    {
        try {
            $this->ordersFacade->changeOrderStatus($values['orderId'], $values['orderStatus']);
        } catch (\Exception $e) {
            ($this->onFailure)('Nepodařilo se změnit stav objednávky!');
            return;
        }
        ($this->onSuccess)('Stav objednávky byl změnen.');
    }
}