<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Mock_Ldap library.
 *
 *
 * @package    Mock_Ldap
 * @author     skeen@mozilla.org
 * @license    http://www.opensource.org/licenses/mit-license.php
 */
class Mock_Ldap_Core extends Ldap_Core {

  private $manager_list = '{"atyrrel@mozilla.com":{"cn":"Amie Tyrrel","title":"Executive Assistant",
    "bugzilla_email":"atyrrel@mozilla.com"},"achung@mozilla.com":{"cn":"Anthony Chung",
    "title":"QA Execution Team Manager","bugzilla_email":"tchung@mozilla.com"},
    "bmoss@mozilla.com":{"cn":"Bob Moss","title":"Engineering Director",
    "bugzilla_email":"bmoss@mozilla.com"},"chris@mozilla.com":{"cn":"Chris Beard",
    "title":"Chief Innovation Officer","bugzilla_email":"cbeard@mozilla.org"},
    "clyon@mozilla.com":{"cn":"Chris Lyon","title":"Director of Infrastructure Security",
    "bugzilla_email":"clyon@mozilla.com"},"blizzard@mozilla.com":{"cn":"Christopher Blizzard",
    "title":"Director of Evangelism","bugzilla_email":"blizzard@mozilla.com"},
    "ctalbert@mozilla.com":{"cn":"Clint Talbert","title":"Automation & Tools Engineering Lead",
    "bugzilla_email":"ctalbert@mozilla.com"},"dsicore@mozilla.com":{"cn":"Damon Sicore",
    "title":"Sr. Director of Platform Engineering","bugzilla_email":"dsicore@mozilla.com"},
    "dportillo@mozilla.com":{"cn":"Dan Portillo","title":"VP, Organizational Development",
    "bugzilla_email":"dportillo@mozilla.com"},"deinspanjer@mozilla.com":{"cn":"Daniel Einspanjer",
    "title":"Metrics Software Engineer","bugzilla_email":"deinspanjer@mozilla.com"},
    "dascher@mozilla.com":{"cn":"David Ascher","title":"CEO, Mozilla Messaging",
    "bugzilla_email":"dascher@mozilla.com"},"dtenser@mozilla.com":{"cn":"David Tenser",
    "title":"Helpful Rockstar","bugzilla_email":"djst@mozilla.com"},
    "dietrich@mozilla.com":{"cn":"Dietrich Ayala","title":"Firefox Engineer",
    "bugzilla_email":"dietrich@mozilla.com"},"handerson@mozilla.com":{"cn":"Harvey Anderson",
    "title":"General Counsel","bugzilla_email":"handerson@mozilla.com"},
    "jfinette@mozilla.com":{"cn":"Jane Finette","title":"Director, Marketing",
    "bugzilla_email":"jfinette@mozilla.com"},"jcook@mozilla.com":{"cn":"Jim Cook",
    "title":"Money Man - VP Finance","bugzilla_email":"jcook@mozilla.com"},
    "lilly@mozilla.com":{"cn":"John Lilly","title":"CEO","bugzilla_email":"lilly@mozilla.com"},
    "joduinn@mozilla.com":{"cn":"John O\'Duinn","title":"Director, Release Engineering",
    "bugzilla_email":"joduinn@mozilla.com"},"jslater@mozilla.com":{"cn":"John Slater",
    "title":"Creative Director","bugzilla_email":"jslater@mozilla.com"},
    "jnightingale@mozilla.com":{"cn":"Johnathan Nightingale","title":"Director of Firefox Development",
    "bugzilla_email":"johnath@mozilla.com"},"jst@mozilla.com":{"cn":"Johnny Stenback",
    "title":null,"bugzilla_email":"jst@mozilla.com"},"justin@mozilla.com":{"cn":"Justin Fitzhugh",
    "title":"VP, Engineering Operations","bugzilla_email":"justin@mozilla.com"},
    "ladamski@mozilla.com":{"cn":"Lucas Adamski","title":"Director of Security Engineering",
    "bugzilla_email":"ladamski@mozilla.com"},"kijima@mozilla-japan.org":{"cn":"Makoto Kijima",
    "title":"Marketing Director","bugzilla_email":"kijima@mozilla-japan.org"},
    "mary@mozilla.com":{"cn":"Mary Colvig","title":null,"bugzilla_email":"mary@mozilla.com"},
    "mevans@mozilla.com":{"cn":"Matt Evans","title":"Director QA","bugzilla_email":"mevans4900@gmail.com"},
    "mshapiro@mozilla.com":{"cn":"Melissa Shapiro","title":"Spin Director",
    "bugzilla_email":"mshapiro@mozilla.com"},"beltzner@mozilla.com":{"cn":"Mike Beltzner",
    "title":"Mise en Sc\u00e8ne, Firefox","bugzilla_email":"beltzner@mozilla.com"},
    "morgamic@mozilla.com":{"cn":"Mike Morgan","title":"Director of Web Development",
    "bugzilla_email":"morgamic@gmail.com"},"shaver@mozilla.com":{"cn":"Mike Shaver",
    "title":"VP, Engineering","bugzilla_email":"shaver@mozilla.org"},
    "mitchell@mozilla.com":{"cn":"Mitchell Baker","title":"Chief Lizard Wrangler, Chairman, MoFo and Chairman, MoCo",
    "bugzilla_email":"mitchell@mozilla.com"},"nnguyen@mozilla.com":{"cn":"Nick Nguyen",
    "title":"Director of Add-ons","bugzilla_email":"nnguyen@mozilla.com"},
    "rocallahan@mozilla.com":{"cn":"Robert O\'Callahan","title":null,
    "bugzilla_email":"roc@ocallahan.org"},"rsayre@mozilla.com":{"cn":"Robert Sayre",
    "title":"Teddy Bear","bugzilla_email":"sayrer@gmail.com"},"sbindernagel@mozilla.com":{"cn":"Seth Bindernagel",
    "title":"Director, Localization","bugzilla_email":"sethb@mozilla.com"},
    "pavlov@mozilla.com":{"cn":"Stuart Parmenter","title":"Director, Mobile",
    "bugzilla_email":"pavlov@pavlov.net"},"tnitot@mozilla.com":{"cn":"Tristan Nitot",
    "title":"President and Founder, Mozilla Europe","bugzilla_email":"tnitot@mozilla.com"},
    "vladimir@mozilla.com":{"cn":"Vladimir Vukicevic","title":"Principal Engineer, Firefox",
    "bugzilla_email":"vladimir@mozilla.com"},"mzeier@mozilla.com":{"cn":"matthew zeier",
    "title":"Director IT\/Ops","bugzilla_email":"mzeier@mozilla.com"}}';
  

  public function  __construct($config, $credentials) {
    kohana::log('debug',"**USING MockLdap**");
    return parent::__construct($config, $credentials);
  }

  public function manager_list() {
    kohana::log('debug',"Called MOCK ".__METHOD__);
    return json_decode($this->manager_list, true);
  }
  public function employee_attributes($ldap_email) {
    kohana::log('debug',"Called MOCK ".__METHOD__);
    $manager_list = json_decode($this->manager_list, true);
    return isset($manager_list[$ldap_email])
      ? $manager_list[$ldap_email]
      : array();
  }
}