# Securing your web applications using the Symfony Security component

The code from [the conference of AFUP](https://www.meetup.com/fr-FR/afup-paris-php/events/253944518/)
that was held 28/08/2018 at JoliCode, Paris.

The slides are available [on my slideshare](https://fr.slideshare.net/VladyslavRiabchenko/scurisation-de-vos-applications-web-laide-du-composant-security-de-symfony).

:arrow_left: [Task 1](/vria/symfony-security-component-use/tree/1-primitive-listener/) | Task 2 | [Task 3](/vria/symfony-security-component-use/tree/3-anonymous-token) :arrow_right:
--- | --- | ---

*Centralize authentication in a firewall so that you can use multiple authentication systems.*

Firewall (Symfony\Component\Security\Http\Firewall) is a security listener that 
uses a FirewallMap to register security listeners for the given request.
Firewall allows for several authentication systems in single application.
It also help to enable a security system conditionally, e.g. under url that starts with "/main".

Resume of the changes:
- in [index.php] the Kernel instance is created to treat the user's request,
- in [index.php] the Event dispatcher is created to dispatch events (e.g. KernelEvents::REQUEST),
- [Controller] and [ControllerResolver] are added to produce a response to any request,
- SecurityListener is renamed to [MainSecurityListener] to emphasize the fact that this 
listener is not the only security listener it is one among the others, 
- [MainSecurityListener] implements [Symfony\Component\Security\Http\Firewall\ListenerInterface]
because Firewall requires that all security listener implement it,
- Firewall hooks to KernelEvents::REQUEST event and activates [MainSecurityListener] 
only when the request path starts with "/main". 

Urls to test:

* `/main?auth_user=vlad&auth_pw=pass` (authenticated)
* `/main?auth_user=gordon&auth_pw=freeman05` (not authenticated)
* `/secondary?auth_user=vlad&auth_pw=pass` (not authenticated)

Urls to test without rewrite rules must start with `/index.php`, e.g. `/main?auth_user=vlad&auth_pw=pass`.

[index.php]: public/index.php
[Controller]: src/Controller.php
[ControllerResolver]: src/ControllerResolver.php
[MainSecurityListener]: src/Security/MainSecurityListener.php
