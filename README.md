# fansubebooks.chaostangent.com

Fansub Ebooks is a very silly Twitter bot and associated website for tweeting random lines from anime fansub scripts.

It was conceived in the dark winter months of 2012 and has been tweeting nonsense ever since.

## Twitter bot

The [Twitter bot](https://twitter.com/fansub_ebooks) is the main interaction point for the project and updates every half an hour (give or take a few seconds).

## Website

The associated Fansub Ebooks website allows visitors to vote on which lines they want to see tweeted next as well as view the full library of out-of-context nonsense.

## Development

Originally a Symfony 2.1 project, the latest version is based on Symfony 2.7 for both automated tweeting and the website. The database was originally MySQL but is now Postgres.

## Helping

### Development

If you want to help with the development of Fansub Ebooks (and I can only imagine why you would), getting set up should be fairly straightforward:

1. Clone the current repository with git
2. Download and install [Vagrant](https://www.vagrantup.com/) if you haven't already
3. Grab the [Vagrant hostmanager](https://github.com/smdahlen/vagrant-hostmanager) plugin and install it
3. Run `vagrant up` from the main repo directory
4. Wait for the development virtual machine to be provisioned
5. Visit [http://fansubebooks.chaostangent.local/app_dev.php/](http://fansubebooks.chaostangent.local/app_dev.php/) and develop!

### Suggest series

If you want to see a particular anime series in the pool of lines, you can [suggest one](http://fansubebooks.chaostangent.com/help).

### Contribute scripts

If you're a fansub group or just interested in helping grow the library of available lines you can [contribute scripts](http://fansubebooks.chaostangent.com/help).

Also, if you have the much maligned script from AnimeJunkies Ghost in the Shell Standalone Complex episodes, drop what you're doing and talk to me.
