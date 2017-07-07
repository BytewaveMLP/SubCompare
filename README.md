# SubCompare

[![standard-readme compliant](https://img.shields.io/badge/readme%20style-standard-brightgreen.svg?style=flat-square)](https://github.com/RichardLitt/standard-readme)

> A small Slim 3 application to compare the subscriptions of two YouTube channels

SubCompare is, as the name would suggest, a simple application that utilizes the YouTube Data API to fetch the subscriptions of two YouTube channels and perform some basic comparison between them. It takes two YouTube channel IDs as input, and produces a comparison breakdown of the common subscriptions between both channels as well as the subscriptions unique to one or the other.

## Table of Contents

- [Background](#background)
- [Install](#install)
    - [Prerequisites](#prerequisites)
	- [Obtaining an API key](#obtaining-an-api-key)
	- [Installation](#installation)
- [Usage](#usage)
- [Maintainers](#maintainers)
- [Contribute](#contribute)
- [License](#license)

## Background

SubCompare was created at the request of a friend after they failed to find any similar service. It's not a terribly useful niche application, but we programmers like a challenge. :)

## Install

### Prerequisites

- Apache 2/nginx
- PHP >= 7.0
- CLI access + Composer
- [A YouTube Data API key](#obtaining-an-api-key)

### Obtaining an API key

To obtain a YouTube Data API key, [click here](https://console.developers.google.com/apis) and **enable the YouTube Data API v3**. Then, go to the Credentials section, and **create an _API key_**. Add optional restrictions as you see fit, and copy the API key into `.env` post-installation.

### Installation

```
$ git clone https://github.com/BytewaveMLP/SubCompare
$ cd SubCompare
$ composer install
$ cp .env.example .env # assuming composer didn't do this for you
$ $EDITOR .env
```

In `.env`, **fill out the required `YT_API_KEY` config option** (see [Obtaining an API key](#obtaining-an-api-key) for info).

At this point, install your `nginx` configs if necessary and restart your webserver.

## Usage

Simply browse to your configured domain. SubCompare will present a form with two boxes for channel IDs. Just paste the channels you want to compare into the box, and click Submit. SubCompare will handle the rest.

## Maintainers

- [BytewaveMLP](https://github.com/BytewaveMLP)

## Contribute

**Issues, suggestions, or concerns?** Submit a GitHub issue!

**Want to add a feature?** We accept PRs!

## License

Copyright (c) Eliot Partridge, 2016-17. Licensed under [the MPL v2.0](/LICENSE).
