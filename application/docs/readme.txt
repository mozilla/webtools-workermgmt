Defining a new Bug Filing

  a bug filing is simply a form that takes input and from that input, files one
    or more bugs in bugzilla.

0. This code utilizes the xmlrpc interface to bugzilla so read
     http://www.bugzilla.org/docs/tip/en/html/api/Bugzilla/WebService/Bug.html#Bug_Creation_and_Modification



1. Create the Form View: Nothing special here, see application/views/pages for
     examples.  Just need a form to gather all the input needed to build the
     bug(s) for your workflow (i.e. Newhire, New Webdev Project, etc)



2. Build a controller that extends Controller (application/classes/controller.php)
     to handle the Form submission

   Do all the normal Kohana stuff to validate the form input as needed, once that
     is done, do something like this: ($this->file_these() being the end goal)
     See application/classes/controller/hiring and .../webdev for examples

   if ($post->check()) {
        $form = arr::overwrite($form, $post->as_array());
        $bugs_to_file = array('Newhire_Setup','Newhire_Contractor');
        if($form['mail_needed']) {
            $bugs_to_file[] = 'Newhire_Email';
        }
        // File the appropriate Bugs
        if($this->file_these($bugs_to_file, $form)) {
            $this->request->redirect('hiring/contractor');
        }
    }



3. Complete a section in config file:
     application/config/workermgmt.php , $config['bug_defaults'] to set the
     static values for your bug(s). (values that do not require access to the
     submitted form values to construct and are always the same for the bug being
     filed.  'product' and 'component', among others are often defined here.



4. extend Filing class (i.e classes/filing/newhire/setup.php) and do 2 things.

    1. set protected $label.  This is the 'meant for humans' label for this
       type of bug.  It is used for messaging and feedback.

    2. override contruct_content() (be sure to call parent::contruct_content() in
       the first line of you method)
       see application/classes/filing for examples

       This simply takes the $submitted_data (that most likely came from a web
       form submission) and uses that to build values for the Filing attributes
       (see Filing->attributes).  These are what will be sent to the Bugzilla
       xmlrpc call.
       Essentially, it is where you build any values for bug that you could not define in
       $config['bug_defaults'] because they require values from the submitted
       form or have other business logic involved in their construction.
       'description' among others are often defined here
 