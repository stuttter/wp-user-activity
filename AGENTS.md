# WP User Activity contributor guidance

## Compatibility

- Preserve PHP 7.4 and WordPress 6.4 compatibility unless a dedicated pull
  request explicitly changes the published minimums.
- Treat activity insertion, metadata, capabilities, retention, IP addresses,
  user agents, and multisite behavior as elevated-risk code.
- Preserve public functions, hooks, filter arguments, activity type IDs,
  metadata keys, post type behavior, and extension classes unless a deprecation
  path is part of the change.

## Tests

- Add a regression test before changing observed behavior.
- Characterize stored metadata, capability mapping, action messages, retention,
  user identity, request context, and loop prevention when changing those paths.
- Run `composer test`, the declared PHP syntax matrix, and metadata/artifact
  validation before requesting review.

## Releases

The source, readme stable tag, Git tag, and WordPress.org version must agree
before publishing. Do not infer a release version from only one of them.

## Automation

Follow the organization-level safety boundaries. AI-authored implementation
must remain a draft pull request and cannot modify workflows, release policy,
ownership, security policy, or this file.
