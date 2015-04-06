======================================
Brian Krebs Intrusion Detection System
======================================

This project's goal is to scan
krebsonsecurity.com for a users'
place of employment. Upon detection,
the site will automatically acquire a
list of available job openings via
the Monster API, and use pre-uploaded
resumes, available skills, and coverletters
to apply to these openings.

Furthermore, coverletters will be pre-populated
with buzzwords pulled from each job posting for
the best (most hilarious) possible effect on a
hiring manager. This "Coverletter Mad-Libs" will
be generated after the user has either selected
a pre-generated coverletter template, or created
their own.

======================================
Progress
======================================

Access Control Plugin - Complete (needs modification for each added action and controller)
User Registration - Complete
User Login - Complete
Resume Uploading - Complete
Skills tracking - In Progress
Company Aliasing - In Progress
Coverletter Templating - In Progress
Mad-Lib Engine - Not Started
krebsonsecurity.com scanner - Not Started
Auto-application processor - not started
Frontend cleanup - Not Started

Security testing via SqlMap and manual XSS injection - Up to date with completed functionality

======================================
Requirements for use
======================================

>= PHP 5.3
>= PHalcon 1.3.0

