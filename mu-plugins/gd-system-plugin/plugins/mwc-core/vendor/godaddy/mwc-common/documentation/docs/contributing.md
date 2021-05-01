---
id: contributing
title: Contributing
---

## Issues and Bugs

We encourage active team collaboration and contribution to our greater code base.  If you have discovered an issue or edge case within the core library, it is strongly encouraged to submit a pull request in order to resolve that issue for the greater team rather than simply logging an issue / ticket.

:::note Minimum Expectations
If you are unable to fix the issue yourself, you are expected to **at a minimum** provide a pull request with a failing test for the given scenario!
:::

This library is used as a central code store for the broader team and it is imperative that we keep it up to date fixing technical debt when it is an inconsequential effort versus allowing it to grow to a point where it has a broad impact on the team at large.

When providing a pull request to resolve an issue in the core library, you should be ideally providing the following in conjunction with the [pull request bug template](#):

- A clear description of the issue
- A failing test demonstrating the issue
- A description of what would you expect the functionality to produce if the bug did not exist

The above is required in the hope that team members experiencing the same problem are able to collaborate quickly for a clear resolution, in addition to, ensuring the problem and expectations are clearly defined.

## Common Development Proposals

TBD: We should define the process by which someone proposes new additions before we have a larger team.  We would not want people just randomly sticking things in a shared library with a single random PR approval.

## Coding Style

Contributions to this library and our broader projects should confirm to [PSR](https://github.com/php-fig/fig-standards/tree/master/accepted) coding standards and the [PSR-4](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md) autoloading standard.

:::note Coding Style Standards
[PSR-2](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md) sets out the general code style standards.  In addition, [PSR Naming Conventions](https://www.php-fig.org/bylaws/psr-naming-conventions/) are explicitly set out to follow standard patterns and should be adhered to.
:::
