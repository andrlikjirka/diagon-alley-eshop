<?php

namespace App\PublicModule\Components\ProductCardControl;

use App\Model\Facades\ProductsFacade;
use App\Model\Orm\Products\Product;
use App\PublicModule\Forms\AddProductToCartFormFactory;
use Nette\Application\UI\Control;
use Nette\Forms\Form;

/**
 * Class ProductCardControl
 * @package App\PublicModule\Components
 * @author Jiří Andrlík
 */
class ProductCardControl extends Control
{

    public function __construct(
        private readonly AddProductToCartFormFactory $addProductToCartFormFactory
    )
    {}

    /**
     * Metoda vykreslující šablonu komponenty
     * @param Product $product
     * @return void
     */
    public function render(Product $product): void
    {
        $this->template->product = $product;

        $form = $this->getComponent('addProductToCartForm');
        $form->setDefaults(['productId' => $product->id]);

        $this->template->render(__DIR__ . '/templates/default.latte');
    }

    /**
     * Metoda vykreslující komponentu produktu v celostránkovém zobrazení
     * @param Product $product
     * @return void
     */
    public function renderShow(Product $product): void
    {
        $this->template->product = $product;

        $form = $this->getComponent('addProductToCartForm');
        $form->setDefaults(['productId' => $product->id]);

        $this->template->render(__DIR__ . '/templates/show.latte');
    }

    public function createComponentAddProductToCartForm(): Form
    {
        $onSuccess = function (string $message) {
            $this->presenter->flashMessage($message, 'success');
            $this->presenter->redirect('this');
        };

        $onFailure = function (string $message) {
            $this->presenter->flashMessage($message, 'success');
            $this->presenter->redirect('this');
        };

        return $this->addProductToCartFormFactory->create($onSuccess, $onFailure);
    }

}