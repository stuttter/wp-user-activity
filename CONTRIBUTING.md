# Contributing

Thanks for helping maintain WP User Activity.

## Before changing behavior

Describe the observable behavior, compatibility expectations, and acceptance
criteria in a GitHub issue. Security reports belong in the private reporting
channel described in `SECURITY.md`.

## Pull requests

- Keep each pull request focused and reversible.
- Add regression coverage for behavior changes and bug fixes.
- Preserve the declared PHP and WordPress minimum versions.
- Describe activity storage, retention, privacy, capability, and multisite impact.
- Exercise the affected action type and its create, update, delete, or status path.
- Do not commit credentials, personal data, production logs, build caches,
  development databases, or generated release ZIP files.
- Wait for every required check and resolve review conversations before merge.

AI-assisted contributions are welcome, but the contributor remains responsible
for understanding and validating the result.

## Development requirements

The plugin and its Composer development toolchain require PHP 7.4 or newer.
Production Composer installs should omit development dependencies.
