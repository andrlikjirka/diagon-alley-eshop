<?php

namespace App\PublicModule\Presenters;

use App\Model\Facades\CategoriesFacade;
use App\Model\Facades\ProductsFacade;
use App\PublicModule\Components\ProductCardControl\ProductCardControl;
use App\PublicModule\Components\ProductCardControl\ProductCardControlFactory;
use Nette\Application\AbortException;
use Nette\Utils\Paginator;

class ProductsPresenter extends BasePresenter
{
    /** @persistent */
    public ?string $categorySlug = null;

    /** @persistent */
    public int $page = 1;

    public function __construct(
        private readonly ProductCardControlFactory $productControlFactory,
        private readonly ProductsFacade            $productsFacade,
        private readonly CategoriesFacade          $categoriesFacade
    )
    {
        parent::__construct();
    }

    /**
     * Akce pro výpis produktů
     * @return void
     * @throws AbortException
     */
    public function renderDefault(): void
    {
        if (!empty($this->categorySlug)) {
            try {
                $category = $this->categoriesFacade->getCategoryBySlug($this->categorySlug);
            } catch (\Exception $e) {
                $this->flashMessage('Kategorie nenalezena.', 'warning');
                $this->redirect('this', ['categorySlug' => null]);
            }
            $this->template->currentCategory = $category;
            $products = $this->productsFacade->findShowedProductsByCategoryRecursively($category); //produkty vy dané kategorii + produkty ve všech podřízených kategoriích
        } else {
            $products = $this->productsFacade->findAllShowedProducts();
        }

        #region paginator
        $paginator = new Paginator();
        $paginator->setItemCount($products->countStored());
        $paginator->setItemsPerPage(6);
        $paginator->setPage($this->page);
        #endregion

        $offset = $paginator->offset;
        $limit = $paginator->length;

        $this->template->paginator = $paginator;
        $this->template->products = $products->limitBy($limit, $offset);
    }

	/**
	 * @throws AbortException
	 */
	public function renderShow(string $productSlug): void
	{
        try {
            $product = $this->productsFacade->getProductBySlug($productSlug);
        } catch (\Exception $e) {
            $this->flashMessage('Produkt nenalezen.', 'warning');
            $this->redirect('default');
        }
        if (!$product->showed) {
            $this->flashMessage('Produkt nelze zobrazit.', 'warning');
            $this->redirect('default');
        }
        $this->template->product = $product;
    }

    public function createComponentProductCard(): ProductCardControl
    {
        return $this->productControlFactory->create();
    }

}