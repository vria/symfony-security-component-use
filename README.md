# Securing your web applications using the Symfony Security component

The code from [the conference of AFUP](https://www.meetup.com/fr-FR/afup-paris-php/events/253944518/)
that was held 28/08/2018 at JoliCode, Paris.

The slides are available [on my slideshare](https://fr.slideshare.net/VladyslavRiabchenko/scurisation-de-vos-applications-web-laide-du-composant-security-de-symfony).

| :arrow_left: [Task 3] | Task 4 | [Task 5] :arrow_right: |
| --- | --- | --- |

*Abstract the user retrieval logic and move it out of the security listeners.*

[MainSecurityListener] should be independent of the method the users are stored and fetched.
This logic is encapsulated in user providers that implement `Symfony\Component\Security\Core\User\UserProviderInterface`.
For instance, [InMemoryUserProvider] is used to store users in memory.
[MainSecurityListener] therefore will delegate the searching of users to user provider.

Resume of changes:
- In [MainSecurityListener] `$userProvider` is required as a constructor argument.
- In [MainSecurityListener] user provider is used to try to retrieve the user.
If user is not found or password does not fit then token is not created.
- In [index.php] `$mainUserProvider` is created with one user and passed to [MainSecurityListener].

Urls to test:

* `/main?auth_user=gordon&auth_pw=freeman` (authenticated as "gordon")
* `/main` (authenticated as "anon.")
* `/secondary?auth_user=gordon&auth_pw=freeman` (not authenticated)

Urls to test without rewrite rules must start with `/index.php`, e.g. `/index.php/main?auth_user=gordon&auth_pw=freeman`.

[index.php]: public/index.php
[MainSecurityListener]: src/Security/MainSecurityListener.php
[Task 3]: https://github.com/vria/symfony-security-component-use/tree/3-anonymous-token
[Task 5]: https://github.com/vria/symfony-security-component-use/tree/5-authentication-provider
