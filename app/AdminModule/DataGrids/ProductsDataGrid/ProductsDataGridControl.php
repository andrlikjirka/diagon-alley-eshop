<?php

declare(strict_types=1);

namespace App\AdminModule\DataGrids\ProductsDataGrid;


use App\Model\Facades\ProductsFacade;
use App\Model\Orm\Products\Product;
use Nette\Application\AbortException;
use Nette\Application\UI\Control;
use Nette\Bridges\ApplicationLatte\Template;
use stdClass;
use Ublaboo\DataGrid\DataGrid;

/**
 * Class ProductsDataGrid
 * @package App\AdminModule\DataGrids\ProductsDataGrid
 * @author Martin Kovalski
 *
 * @property-read Template|stdClass $template
 */
class ProductsDataGridControl extends Control
{
	public function __construct(
		private readonly ProductsFacade $productsFacade
	) {}

	public function createComponentDataGrid(): DataGrid
	{
		$grid = new DataGrid();

		$grid->setDataSource($this->productsFacade->findAllProducts());

		$grid->addColumnNumber('id', 'ID produktu')
			->setAlign('left')
			->setSortable();

		$grid->addColumnText('name', 'Název produktu')
			->setSortable()
			->setAlign('left')
			->setFilterText();

		$grid->addColumnNumber('stock', 'Skladem')
			->setSortable()
			->setAlign('left')
			->setRenderer(function (Product $product): string {
				return $product->stock . " ks";
			});

		$grid->addColumnNumber('price', 'Cena produktu')
			->setAlign('left')
			->setRenderer(function (Product $product): string {
				return $product->galleonPrice . "G " . $product->sicklePrice . "S " . $product->knutPrice . "K";
			});

		$grid->addAction('showed', '')
			->setIcon(function (Product $product): string {
				return $product->showed ? 'eye' : 'eye-slash';
			})
			->setClass('ajax btn btn-xs btn-secondary ms-1 me-1 mb-1');


		$grid->addActionCallback('edit', '')
			->setIcon('pen-to-square')
			->setClass('btn btn-xs btn-warning ms-1 me-1 mb-1')
			->onClick[] = function ($itemId): void {
				$this->presenter->redirect('Products:edit', ['id' => $itemId]);
		};

		$grid->addAction('delete', '')
			->setIcon('trash')
			->setClass('ajax btn btn-xs btn-danger ms-1 me-1 mb-1');

		$grid->setItemsPerPageList([1, 10, 20, 50, 100, 200, 500], false);

		return $grid;
	}

	/**
	 * @throws AbortException
	 */
	public function handleShowed(int $id): void
	{
		$product = $this->productsFacade->getProduct($id);
		$product->showed = !$product->showed;
		try {
			$this->productsFacade->saveProduct($product);
		} catch (\Exception $e) {
			$this->presenter->flashMessage($e->getMessage(), 'danger');
		}

		if($this->presenter->isAjax()) {
			$this->presenter->redrawControl('flashes');
			$this['dataGrid']->redrawItem($product->id);
		} else {
			$this->presenter->redirect('this');
		}
	}

	/**
	 * @throws AbortException
	 */
	public function handleDelete(int $id): void
	{
		$product = $this->productsFacade->getProduct($id);
		$product->deleted = true;
		try {
			$this->productsFacade->saveProduct($product);
			$this->presenter->flashMessage('Produkt byl úspěšně smazán.', 'success');
		} catch (\Exception $e) {
			$this->presenter->flashMessage($e->getMessage(), 'danger');
		}

		if($this->presenter->isAjax()) {
			$this->presenter->redrawControl('flashes');
			$this['dataGrid']->redrawControl();
		} else {
			$this->presenter->redirect('this');
		}
	}

	public function render(): void
	{
		$this->template->render(__DIR__ . '/productsDataGrid.latte');
	}
}