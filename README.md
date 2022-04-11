# Godric

Godric is a membership management and GOTV tool.

It is licensed under the GNU General Public License version 3 or later - see `LICENSE.TXT` for more information.

At this stage in development it is probably only immediately useful to UCU branches.

Solidarity.

## Installation requirements

* Support for PHP8, a database server, access to SMTP
* The Membership Secretary

## Installation process

* Copy the repository to the server
* Set up a database and access credentials
* Copy the `.env.example` file to `.env` and enter the database and SMTP details, and set `APP_NAME` and `APP_URL`
* For some environments you may need to set additional variables; consult the Laravel documentation directly
* Enter the membership ID of the person who will be performing the initial import in the `BOOT_USER` variable (ideally, the Membership Secretary)
* In the root of the repository, run:
```
npm install
composer install
npm run dev
php artisan key:generate
php artisan migrate --seed
```
* Set up your web server to use the `public` folder of the app (**not** the root folder) as the document root
* Go to the location now being served, and log in as the `BOOT_USER` with the password `boot`, then change that password to a secure one
* Use the Import Members link to import a standard membership spreadsheet (obtained by being the Membership Secretary)

## Setting up roles

Use the "Set up Roles" link. There are the following roles currently available, and users need not have any roles at all:

* No role: can set own participation in campaigns
* Representative: can view membership lists and campaign reports for their remit, and set member participation in campaigns
* Campaigner: like a rep, but the role only works to view membership lists when there's an active campaign (useful for cleanly adding temporary helpers)
* Phonebank: can set member participation in campaigns and view campaign reports
* Report View: can view campaign reports (not needed if they have another role which allows this already)
* Superuser: can set up roles, import members, and set up campaigns

Most representatives should have their roles restricted to the area they operate in - often a department, but it might be a job type or membership type. (Note that while superuser roles can be restricted, a superuser can just edit their role to remove that restriction.) Follow the general GDPR principles when granting access.

The Representatives can now log in to view their membership lists, and obtain extracts. You should re-import a standard membership spreadsheet frequently to ensure that these stay up to date.

Phonebank roles are generally better not restricted - they may be contacting people across the organisation, so need to be able to search for anyone. The information they are able to see is restricted to current campaigns.

## Setting up campaigns

A campaign is an action where you would like to record member participation (there is a lot of overlap with structure tests, but they need not be). You can set a percentage threshold for the campaign to reach, and restrict campaigns only to those with voting rights.

Once set up, members can log in to set their participation, representatives can set participation for members in their remit, and superusers can import bulk lists of participants. Representatives will also gain access to lists which filter out participants (or those who have said they definitely will not participate) for the purpose of more targeted communications.

Once the campaign is over, participation will be visible on the membership lists to help planning for future campaigns.

