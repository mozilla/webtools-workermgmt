Defining a new Bug Filing

 - This code utilizes the xmlrpc interface to bugzilla so read
   http://www.bugzilla.org/docs/tip/en/html/api/Bugzilla/WebService/Bug.html

 - Create a class that extends Filing class (i.e classes/filing/newhire/setup.php)

 - The class extending Filing needs 3 simple things

   1) protected $required_input_fields = array(...)

     This is a list of keys that need to be present in the $submitted_data array
       (see Filing::factory($filing_class, $submitted_data, $bz_connector)

   2) protected $label = "Karen/Accounting notification";

     This is the 'meant for humans' label for this type of bug.  It is used
       for messaging and feedback.

   3) implement public function  contruct_content() {}

     This simply takes the $submitted_data (that most likely came from a web
       form submission) and uses that to build values for the Filing attributes
       (see Filing->attributes).  These are what will be sent to the Bugzilla
       xmlrpc call.