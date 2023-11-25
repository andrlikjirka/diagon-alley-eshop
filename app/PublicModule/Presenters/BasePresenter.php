<?php

declare(strict_types=1);

namespace App\PublicModule\Presenters;

use App\Components\UserLoginControl\UserLoginControl;
use App\Components\UserLoginControl\UserLoginControlFactory;
use App\PublicModule\Components\NavbarControl\NavbarControl;
use App\PublicModule\Components\NavbarControl\NavbarControlFactory;
use Nette\Application\UI\Presenter;
use Nette\Http\Session;
use Nette\Security\User;


/**
 * Class BasePresenter
 * @package App\AdminModule\Presenters
 * @author Martin Kovalski
 */
abstract class BasePresenter extends Presenter
{
	/** @var User @inject */
	public User $user;

	/** @var Session @inject */
	public Session $session;


    /** @var UserLoginControlFactory  */
    private UserLoginControlFactory $userLoginControlFactory;

    /** @var NavbarControlFactory  */
    private NavbarControlFactory $navbarControlFactory;
    /**
     * Base Presenter contstructor
     * @param UserLoginControlFactory $userLoginControlFactory
     * @param NavbarControlFactory $navbarControlFactory
     */

    /**
     * Tovární metoda pro začlenění komponenty UserLoginControl
     * @return UserLoginControl
     */
	public function createComponentUserLogin(): UserLoginControl
    {
        return $this->userLoginControlFactory->create();
    }

    /**
     * Tovární metoda pro začlenění komponenty Navbar
     * @return NavbarControl
     */
    public function createComponentNavbar(): NavbarControl
    {
        return $this->navbarControlFactory->create();
    }

    #region injects
    public function injectUserLoginControlFactory (UserLoginControlFactory $userLoginControlFactory): void
    {
        $this->userLoginControlFactory = $userLoginControlFactory;
    }

    public function injectNavbarControlFactory (NavbarControlFactory $navbarControlFactory): void
    {
        $this->navbarControlFactory = $navbarControlFactory;
    }
    #endregion injects

}