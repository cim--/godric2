# Features needed

## Optimisations

* Large member lists take ages to calculate when many campaigns present. Need to pre-calculate rather than N+1

## For balloting / campaigning

* Campaigns: bulk move wait -> yes

## More generally

* Highlight permissions held on reps list
* More structured list of branch offices and holders (can be done as a notice, of course)
** could link to roles/permissions in-app as well, potentially?

## Interface improvements

* Doing a password reset on an account which doesn't exist but could should create it and move to the initial password set stage
* Have a specific "create account" form on the main login page (just cosmetic, but different field labels)

## Data retention improvements

* Implement "dispute+3" cleanup of old campaigns. Probably needs a dispute object to link them to so that it can be configured properly. (No hurry, just sometime before 2025)

## For testing

* More tests for workplaces
* More tests for ballots
