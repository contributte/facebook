# Facebook

Easy-to-use Facebook wrapper for [`Nette Framework`](https://github.com/nette/).

Using library
https://github.com/thephpleague/oauth2-facebook

because Facebook SDK library is not developed anymore.
https://github.com/facebookarchive/php-graph-sdk/issues/1217

## Content

- [Setup](#setup)
- [Configuration](#configuration)
- [Usage](#usage)

## Setup

```bash
composer require contributte/facebook
```

```neon
extensions:
	facebook: Contributte\Facebook\DI\Nette\FacebookExtension
```

## Configuration

You need to create a FacebookApp and supply these parameters:

* **clientId**
* **clientSecret**
* **graphApiVersion**

```neon
facebook:
	clientId: %yourAppId%
	clientSecret: %yourAppSecret%
	graphApiVersion: "v11.0" # https://developers.facebook.com/docs/graph-api/changelog/
```

## Usage

Simple example how to use Facebook Login in Presenter

```php
namespace App\Presenters;

use Contributte\Facebook\Exceptions\FacebookLoginException;
use Contributte\Facebook\FacebookLogin;
use Nette\Application\Responses\RedirectResponse;
use Nette\Application\UI\Presenter;
use Nette\Security\AuthenticationException;

final class SignPresenter extends Presenter
{

	/** @var FacebookLogin @inject */
	public $facebookLogin;

	private function getFBAuthorizeUrl(): string
	{
	    return $this->link('//facebookAuthorize');
	}

	public function actionFacebook()
	{
		// Redirect to FB and ask customer to grant access to his account
		$url = $this->facebookLogin->getLoginUrl($this->getFBAuthorizeUrl());
		$this->sendResponse(new RedirectResponse($url));
	}

	/**
	 * Log in user with accessToken obtained after redirected from FB
	 * @return void
	 */
	public function actionFacebookAuthorize()
	{
		$url = $this->getFBAuthorizeUrl();
		$code = $this->getParameter('code');

		// Fetch User data from FB and try to login
		try {
			$user = $this->facebookLogin->getMe($code, $url);

			$identity = new SimpleIdentity(
				1, // id
				'user', // role
				['name' => $user->getEmail()]
			);
			$this->user->login($identity);
			$this->flashMessage('Login successful :-).', 'success');
		} catch (FacebookLoginException | AuthenticationException $e) {
			$this->flashMessage("Login failed. :-( Try again. {$e->getMessage()}", 'danger');
		}

		$this->redirect(':Homepage:');
	}



	public function handleLogout()
	{
		$this->user->logout(true);
		$this->redirect(':Homepage:');
	}

}

```

Template

```latte
	<a href="{plink :Sign:facebook}" rel="nofollow" class="btn btn-primary btn-facebook">
		FB Login
	</a>
	{if $user->isLoggedIn()}
		<a href="{plink logout!}" rel="nofollow" class="btn btn-default">
			logout
		</a>
	{/if}
```
