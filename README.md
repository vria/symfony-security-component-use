# Securing your web applications using the Symfony Security component

The code from [the conference of AFUP](https://www.meetup.com/fr-FR/afup-paris-php/events/253944518/)
that was held 28/08/2018 at JoliCode, Paris.

The slides are available [on my slideshare](https://fr.slideshare.net/VladyslavRiabchenko/scurisation-de-vos-applications-web-laide-du-composant-security-de-symfony).

| :arrow_left: [Task 4] | Task 5 | [Task 6] :arrow_right: |
| --- | --- | --- |

*Add password encryption, accept only activated users. Move the authentication logic out of the security listeners.*

[MainSecurityListener] should be independent of the method the tokens are authentified.
This logic is encapsulated in authentication managers that implement `Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface`.
All that security listeners should do is to extract credentials from request,
construct unauthentified token, pass it to authentication manager and then put the authentified token in `TokenStorage`.

The only task of authentication managers is to receive an unauthentified token and return an authentified token.
For instance, `DaoAuthenticationProvider` relies on user provider to retrieve users and on password encoders 
to verify password. There is also a user checker (`$mainUserChecker`) that verifies whether the user is active.

Resume of changes:
- In [MainSecurityListener] `$authenticationManager` is required as a constructor argument.
- In [MainSecurityListener] authentication manager is used to authenticate token.
If token is not authenticated then no token is created.
- In [index.php] in `$mainUserProvider` the user's password must be encoded and `enabled` property should be defined.
- In [index.php] in `$mainUserChecker` of type `Symfony\Component\Security\Core\User\UserChecker` is created.
- In [index.php] in `$encoderFactory` is created that maps the class of user to a password encoder.
In this case it returns the instance of `Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder` 
for `Symfony\Component\Security\Core\User\User` class.
- In [index.php] in `$mainAuthProvider` of type `Symfony\Component\Security\Core\Authentication\Provider\DaoAuthenticationProvider` 
is created and passed to [MainSecurityListener].

Urls to test:

* `/main?auth_user=gordon&auth_pw=freeman` (authenticated as "gordon")
* try to set `enabled` to false for `gordon` and acess `/main?auth_user=gordon&auth_pw=freeman` (authenticated as "anon.")
* `/main` (authenticated as "anon.")
* `/secondary?auth_user=gordon&auth_pw=freeman` (not authenticated)

Urls to test without rewrite rules must start with `/index.php`, e.g. `/index.php/main?auth_user=gordon&auth_pw=freeman`.

[index.php]: public/index.php
[MainSecurityListener]: src/Security/MainSecurityListener.php
[Task 4]: https://github.com/vria/symfony-security-component-use/tree/4-user-provider
[Task 6]: https://github.com/vria/symfony-security-component-use/tree/6-http-basic
