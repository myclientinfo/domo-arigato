# Domo Arigato Mr Roboto

A trivial demo app to show the creation of a vanilla PHP engine. It is written as a PHP7 application and needs a compatible interpreter to run.

It includes a test suite of unit tests written for PHP Unit, which do not require anything specific to execute. One of those unit tests loads a series of command sequences from a text file, which should fulfill that part of the requirements.
 
The chief interaction is through a simple webpage, which can be accessed at the root by opening it with any web server. The web page is JavaScript enabled and will run commands back to an interaction page - `command.php` - which will execute them. The JavaScript then uses the response of this interaction to  get new positions and orientations, moving it accordingly.
 
Initially only the `PLACE` command is available and the robot is hidden. Using place enables the robot and its navigation buttons. The robot is represented by a circle icon with an arrow.
 
 ## Implementing
 
 1. Clone or checkout the branch from Github
 2. Change to that directory in the command line
 3. Run the command `composer install` to get the dependencies used for testing
 4. Enter command `php -S localhost:8000`
 5. Navigate a modern browser to `http://localhost:8000`
 
 From there you can enter commands through the interface, and navigate the robot around. Note that `REPORT` is not an explicit command, but every other command returns the output from the `REPORT`.
 
 
 ## Limitations and mistakes
 
 * The JavaScript here is created for ES2016/ES2017 and is used without transpiling or polyfilling, so a current browser is needed.
 * Too much time was spent on the web interface, rather than more extensive unit testing.
 * The extensive use of exceptions may be verging on "flow control", and makes testing more difficult
 * There is an annoying animation bug caused by the internals normalising angle, so -90° is normalised to 270°, 360° is normalised to 0°, etc. This results in the animation spinning round backwards at times. 