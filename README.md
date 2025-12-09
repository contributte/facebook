![](https://heatbadger.vercel.app/github/readme/contributte/facebook/?deprecated=1)

<p align=center>
    <a href="https://bit.ly/ctteg"><img src="https://badgen.net/badge/support/gitter/cyan"></a>
    <a href="https://bit.ly/cttfo"><img src="https://badgen.net/badge/support/forum/yellow"></a>
    <a href="https://contributte.org/partners.html"><img src="https://badgen.net/badge/sponsor/donations/F96854"></a>
</p>

<p align=center>
    Website 🚀 <a href="https://contributte.org">contributte.org</a> | Contact 👨🏻‍💻 <a href="https://f3l1x.io">f3l1x.io</a> | Twitter 🐦 <a href="https://twitter.com/contributte">@contributte</a>
</p>

## Disclaimer

| :warning: | This project is no longer being maintained.
|---|---|

| Composer | [`contributte/facebook`](https://packagist.org/packages/contributte/facebook) |
|---|------------------------------------------------------------|
| Version | ![](https://badgen.net/packagist/v/contributte/facebook)      |
| PHP | ![](https://badgen.net/packagist/php/contributte/facebook)    |
| License | ![](https://badgen.net/github/license/contributte/facebook)   |

## About

Easy-to-use Facebook login wrapper for [`Nette Framework`](https://github.com/nette/).

## Installation

```bash
composer require contributte/facebook
```

## Setup

```neon
extensions:
	facebook: Contributte\Facebook\DI\Nette\FacebookExtension
```

If you are using PHP 8.0+, you need to use forked version of `facebook/graph-sdk`. You can rely on our fork [holyfork/facebook-graph-sdk](https://github.com/holyfork/facebook-graph-sdk), it's tested and working.

```json
{
  "repositories": [
    { "type": "git", "url": "https://github.com/holyfork/facebook-graph-sdk" }
  ]
}
```

## Configuration

You need to create a FacebookApp and supply these parameters:

* **appId**
* **appSecret**
* **defaultGraphVersion** (optional)
* **persistentDataHandler** (optional) default value: **session**
* **httpClientHandler** (optional)

```neon
facebook:
	appId: %yourAppId%
	appSecret: %yourAppSecret%
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

	public function actionFacebook()
	{
		// Redirect to FB and ask customer to grant access to his account
		$url = $this->facebookLogin->getLoginUrl($this->link('//facebookAuthorize'), ['email', 'public_profile']);
		$this->sendResponse(new RedirectResponse($url));
	}

	/**
	 * Log in user with accessToken obtained after redirected from FB
	 *
	 * @return void
	 */
	public function actionFacebookAuthorize()
	{
		// Fetch User data from FB and try to login
		try {
			$token = $this->facebookLogin->getAccessToken();

			$this->user->login('facebook', $this->facebookLogin->getMe($token->getValue(), ['first_name', 'last_name', 'email', 'gender']));
			$this->flashMessage('Login successful :-).', 'success');
		} catch (FacebookLoginException | AuthenticationException $e) {
			$this->flashMessage('Login failed. :-( Try again.', 'danger');
		}
	}

}

```

If you need to specify your own state param (more info [here](https://developers.facebook.com/docs/facebook-login/security/#stateparam) mind also checking Enable Strict Mode). `Facebook::getLoginUrl()` takes optional third parameter `$stateParam` which FB passes back unchanged.

## JavaScript Login

You can also use FB login button, for example:

```html
<div
    class="fb-login-button"
    onlogin="fbAfterLogin()"
    data-width="200"
    data-max-rows="1"
    data-size="medium"
    data-button-type="continue_with"
    data-show-faces="false"
    data-auto-logout-link="false"
    data-use-continue-as="true"
    data-scope="email,public_profile"
>
Login
</div>
```

And use `onlogin` event to call backend code which takes care of registration/login process:

```php
/**
 * Log in user with accessToken from cookie/session after javascript authorization
 */
public function actionFacebookCookie()
{
	// Fetch User data from FB and try to login
	try {
		$token = $this->facebookLogin->getAccessTokenFromCookie();

		$this->user->login('facebook', $this->facebookLogin->getMe($token, ['first_name', 'last_name', 'email', 'gender']));
		$this->flashMessage('Login successful :-).', 'success');
	} catch (FacebookLoginException | AuthenticationException $e) {
		$this->flashMessage('Login failed. :-( Try again.', 'danger');
	}
}
```

## Versions

| State       | Version | Branch   | Nette | PHP     |
|-------------|---------|----------|-------|---------|
| dev         | `^0.4`  | `master` | 3.0+  | `>=7.2` |
| stable      | `^0.3`  | `master` | 3.0+  | `^7.2`  |
| stable      | `^0.2`  | `master` | 2.4   | `>=7.1` |

## Development

This package was maintained by these authors.

<a href="https://github.com/f3l1x">
  <img width="80" height="80" src="https://avatars2.githubusercontent.com/u/538058?v=3&s=80">
</a>

-----

Consider to [support](https://contributte.org/partners.html) **contributte** development team.
Also thank you for using this package.
