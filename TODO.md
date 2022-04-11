# Features needed

## For balloting

* Campaign reports: "with wait" / "with wait+help"
* Data entry: more clarity over what 'wait' and 'help' means for a ballot
* Campaigns: automove wait -> yes
* Set up workplace/department map - allow grouping of workplaces, and allow permissions to be allocated on workplaces rather than departments
* Roletype 'campaigner': same permissions as a rep, but *only* when a campaign is active. Makes it easier to allocate and remove temporary permissions.

## More generally

* Petition functionality
* Basic online voting functionality
* Signup functionality that's a bit more generic than a campaign (e.g. picketing slots)
* List of branch offices and holders
** could link to roles/permissions in-app as well, potentially?

## Interface improvements

* Move the 'any role' prefix out of 'reps' (when things are quieter)

## Data retention improvements

* Implement "dispute+3" cleanup of old campaigns. Probably needs a dispute object to link them to so that it can be configured properly. (No hurry, just sometime before 2025)

## For testing

* Some automated browserkit tests would be good
