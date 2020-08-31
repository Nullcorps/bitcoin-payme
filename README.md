# bitcoin-payme

Easy Bitcoin payment page. Enter your xpub, generates addresses locally, only gives out fresh (unused) addresses.

I've just written this as a replacement for the page on my site which does the exact same thing but relies on the blockchair.com api, which is both slow and rate-limited. Page loads were taking 4-5sec which isn't really ideal.

This page uses the bitwasp php bitcoin maths library to *locally* generate addresses based on your xpub (master public key) and it does so using the same derivation path as Electrum wallet (m/0/n), which means these addresses should line up exactly with the addresses in your Electrum wallet, so somebody pays you here and payments land directly in your Electrum wallet.

This was written primarily to give sexworkers an easy, self-custody payment page with no middleman or anyone taking a cut, but whilst also not re-using addresses.

when the page loads it generates a bunch of addresses and then checks through them to see if they've been used (historical or pending balance), and if so, they're skipped over.  

It checks the address balances using public APIs which require no KYC (blockchain.info and soon blockstream.info)

I don't currently know what happens when it runs out of addresses - it really should generate more but I've not seen that case yet so stay tuned in to find out!

If you want a payment page like this but don't have your own hosting, I'll be offering these pages at swpay.me once I've actually written all the Electrum guides and stuff (which is the biggest stumbling block currently).

er - I guess that's about it for the moment. Will be working on the "what happens when we run short of addresses" thing soon :)


You can see how this page looks in practice here: https://swpay.me/donate/
(payments will go towards supporting the project) 


TODO:
=============================
- something to refill addresses once running low
- implement the blockstream api for balances too!
- customisable footer message
- forgot the "back" link (to parent?)
- settings file perhaps? something a bit more elegant than a bunch of textfiles


Setup:
=============================

Once you've cloned the repo to a web folder and can see the page it'll be saying you need to edit index.php and set debug=true; (literally the first line).

That will let you do the setup procedure (enter your xpub, vendor name, vendor 'sig') and get you up and running.

Once setup procedure is complete please set debug back to false as it exposes sensitive information (don't share your xpub!)

Needless to say it's probably a good idea to set up a separate hot wallet (short-term storage wallet) to handle these payments, and then move them to a private wallet or whatever from there (or to your Ca$happ, or exchange if you want to cash out to fiat.

