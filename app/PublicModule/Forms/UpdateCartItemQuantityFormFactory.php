<?php

namespace App\PublicModule\Forms;

use App\Forms\FormFactory;
use App\Model\Facades\CartFacade;
use App\Model\Orm\Carts\Cart;
use Closure;
use Nette\Forms\Form;
use Nette\Utils\ArrayHash;

class UpdateCartItemQuantityFormFactory
{

    private Closure $onSuccess;
    private Closure $onFailure;

    public function __construct(
        private readonly FormFactory $formFactory,
        private readonly CartFacade $cartFacade

    )
    {}

    public function create(int $cartItemId, callable $onSuccess, callable $onFailure): \Nette\Application\UI\Form
    {
        $form = $this->formFactory->create();

        $form->setHtmlAttribute('class', 'ajax');

        $form->addHidden('cartItemId', $cartItemId)
            ->setRequired();

        $form->addInteger('quantity', '')
            ->setRequired()
            ->addRule(Form::INTEGER, 'Počet kusů musí být celé číslo!')
            ->addRule(Form::MIN, 'Počet kusů musí být číslo větší než 1', 1)
            ->setDefaultValue($this->cartFacade->getCartItem($cartItemId)->quantity);

        $form->addSubmit('update', 'Změnit');

        $form->onSuccess[] = $this->formSucceeded(...);

        $this->onSuccess = $onSuccess;
        $this->onFailure = $onFailure;

        return $form;
    }

    public function formSucceeded(Form $form, ArrayHash $values): void
    {
        $cartItem = $this->cartFacade->getCartItem($values['cartItemId']);

        if ($cartItem->product->stock < $values['quantity']) {
            ($this->onFailure)('Do košíku nelze přidat více kusů než je skladem.');
            return;
        }

        try {
            $this->cartFacade->updateCartItem($values['cartItemId'], $values['quantity']);
        } catch (\Exception $e) {
            ($this->onFailure)('Položku v košíku se nepodařilo aktualizovat.');
            return;
        }
        ($this->onSuccess)('Položka v košíku byla aktualizována.');
    }


}