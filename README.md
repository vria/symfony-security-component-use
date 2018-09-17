# Securing your web applications using the Symfony Security component

The code from [the conference of AFUP](https://www.meetup.com/fr-FR/afup-paris-php/events/253944518/)
that was held 28/08/2018 at JoliCode, Paris.

The slides are available [on my slideshare](https://fr.slideshare.net/VladyslavRiabchenko/scurisation-de-vos-applications-web-laide-du-composant-security-de-symfony).

| :arrow_left: [Task 2] | Task 3 | [Task 4] :arrow_right: |
| --- | --- | --- |

*Allow users to authenticate as anonymous.*

When [MainSecurityListener] extract user's credentials it creates `UsernamePasswordToken`
and puts it in `TokenStorageInterface`. Otherwise it does nothing and no token is created.
In this case `AnonymousAuthenticationListener` can create an `AnonymousToken` for you.
This step demonstrates that multiple security listeners can be chained in `FirewallMap`.

Resume of changes:
- In [index.php] `$anonListener` is created.
- In [index.php] `$anonListener` relies on `$anonymousAuthenticationProvider` that is used to validate an anonymous token. 
It must be created by `$anonListener`,
- In [index.php] `$anonListener` is passed to `$firewallMap` **after** [MainSecurityListener].

Urls to test:

* `/main?auth_user=gordon&auth_pw=freeman` (authenticated as "gordon")
* `/main` (authenticated as "anon.")
* `/secondary?auth_user=gordon&auth_pw=freeman` (not authenticated)

Urls to test without rewrite rules must start with `/index.php`, e.g. `/index.php/main?auth_user=gordon&auth_pw=freeman`.

[index.php]: public/index.php
[MainSecurityListener]: src/Security/MainSecurityListener.php
[Task 2]: https://github.com/vria/symfony-security-component-use/tree/2-firewall
[Task 4]: https://github.com/vria/symfony-security-component-use/tree/4-user-provider
