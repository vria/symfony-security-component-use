# Securing your web applications using the Symfony Security component

The code from [the conference of AFUP](https://www.meetup.com/fr-FR/afup-paris-php/events/253944518/)
that was held 28/08/2018 at JoliCode, Paris.

The slides are available [on my slideshare](https://fr.slideshare.net/VladyslavRiabchenko/scurisation-de-vos-applications-web-laide-du-composant-security-de-symfony).

| :arrow_left: [Task 7] | Task 8 | |
| --- | --- | --- |

*Authorize users to access URLs only if they have requested roles.*

Urls to test:

* `/main` and enter "gordon" and "freeman" (authenticated as "gordon")
* `/main` and enter something else then "gordon" and "freeman" (401 Unauthorized, authentication dialog reappears)
* `/main` and enter "g-man" and "bureaucrat" (403 Forbidden)
* `/front` (not authenticated)
* `GET` to `/front/login` shows login form (not authenticated)
*  submit `/front/login` with "gordon" as login and "freeman" as password (403 Forbidden)
*  submit `/front/login` with "g-man" as login and "bureaucrat" as password (redirected to `/front/success`, authenticated as "g-man")
*  while logged as "g-man" under `/front/success` go to `/front/other-page` (authenticated as "g-man")
* `/secondary` (not authenticated, out of all firewalls)

Urls to test without rewrite rules must start with `/index.php`, e.g. `/index.php/main`.

[Task 7]: https://github.com/vria/symfony-security-component-use/tree/7-login-form
