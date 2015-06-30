# eeleventh-hour
EE 2 extension for setting entry expiration time to 11:59PM

*Warning*: there are no CP settings for this extension, it currently only works for one channel whose ID you must hard-code into the file. Plans to add settings are in place but an official roadmap does not exist at this time.

Activation:

First, install it via the CP. Then, change the following line to suit your needs:

`'channel_id' => 'ID of channel this addon will affect'`

Upon saving an entry in the given channel, this addon will modify the timestamp for `expiration_date` to include an 11:59PM time.
