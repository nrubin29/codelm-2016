  <?php
    class Team {
        var $id, $division, $divisionString, $school, $name, $members, $openended, $special;
        
        function __construct($id) {
            $mysql = get_mysql();
            $data = $mysql->query("select * from teams where id = " . $mysql->escape_string($id))->fetch_assoc();
            $this->id = $data["id"];
            $this->division = $data["division"];
            $this->divisionString = $this->division == 0 ? "Demo" : ($this->division == 1 ? "Intermediate" : ($this->division == 2 ? "Advanced" : ($this->division == 3 ? "Practice Intermediate" : ($this->division == 4 ? "Practice Advanced" : "null"))));
            $this->school = $data["school"];
            $this->name = $data["name"];
            $this->members = $data["members"];
            $this->openended = $data["openended"];
            $this->special = $data["special"];
        }

        function get_score() {
            Problem::setup();

            $score = $this->openended;

            foreach (Submission::for_team($this->id) as $submission) {
                $delta = -1;

                if ($submission->result == "100") {
                    $problem = Problem::$all[strval($submission->problem)];
                    $delta = $problem->points;
                }

                else if ($submission->result == "compilation") {
                    $delta = 0;
                }

                $score += $delta;
            }

            return $score;
        }

        function is_solved($problem) {
            foreach (Submission::for_team($this->id) as $submission) {
                if ($submission->problem == $problem && $submission->result == "100") {
                    return true;
                }
            }

            return false;
        }

        function num_solved() {
            $solved = 0;

            foreach (Submission::for_team($this->id) as $submission) {
                if ($submission->result === "100") {
                    $solved++;
                }
            }

            return $solved;
        }

        public static function login($password) {
            $mysql = get_mysql();
            $result = $mysql->query("select id from teams where password = '" . $mysql->escape_string($password) . "'");

            if ($result->num_rows > 0) {
                return $result->fetch_assoc()["id"];
            }

            else {
                return -1;
            }
        }

        public static function register_practice($data) {
            if (self::login($data["password"]) != -1) {
                return -1;
            }

            $mysql = get_mysql();
            $mysql->query("insert into teams (division, password, school, name, members) values (" . $mysql->escape_string($data["division"]) . ", '" . $mysql->escape_string($data["password"]) . "', '" . $mysql->escape_string($data["school"]) . "', 'Practice Team', '" . $mysql->escape_string($data["name"]) . "')");
            return self::login($mysql->escape_string($data["password"]));
        }

        public static function register($data) {
            if (self::login($data["password"]) != -1) {
                return -1;
            }
          
            $mysql = get_mysql();
            $mysql->query("insert into teams (division, password, school, name, members) values (" . ($data["division"] == "one" ? 1 : 2) . ", '" . $mysql->escape_string($data["password"]) . "', '" . $mysql->escape_string($data["school"]) . "', '" . $mysql->escape_string($data["name"]) . "', '" . $mysql->escape_string($data["members"]) . "')");
            //echo("{insert into teams (division, password, school, name, members) values (" . $mysql->escape_string($data["division"]) . ", '" . $mysql->escape_string($data["password"]) . "', '" . $mysql->escape_string($data["school"]) . "', '" . $mysql->escape_string($data["name"]) . "', '" . $mysql->escape_string($data["members"]) . "')}");
            return self::login($mysql->escape_string($data["password"]));
        }
        
        static function get_teams($division) {
            $teams = array();
            
            $mysql = get_mysql();
            foreach ($mysql->query("select id from teams where division = " . $mysql->escape_string($division)) as $row) {
                array_push($teams, new self($row["id"]));
            }

            usort($teams, function ($a, $b) {
                return $b->get_score() - $a->get_score();
            });
            
            return $teams;
        }
    }
    
    class Problem {
        var $id, $display_id, $num, $divisions, $name, $points, $question, $sample, $java, $python, $cpp, $correct, $hand_graded, $case_sensitive, $sort_output, $truncate_numbers;

        function __construct($id, $display_id, $divisions, $name, $points, $question, $sample, $correct, $java, $python, $cpp, $hand_graded=false, $case_sensitive=false, $sort_output=false, $truncate_numbers=false) {
            $this->id = $id;
            $this->display_id = $display_id;
            $this->divisions = $divisions;
            $this->name = $name;
            $this->points = $points;
            $this->question = $question;
            $this->sample = $sample;
            $this->correct = $correct;
            $this->java = $java;
            $this->python = $python;
            $this->cpp = $cpp;
            $this->hand_graded = $hand_graded;
            $this->case_sensitive = $case_sensitive;
            $this->sort_output = $sort_output;
            $this->truncate_numbers = $truncate_numbers;
        }

        public static $all;
        
        static function setup() {
            if (!isset(self::$all)) {
                $report1 = 'here at New Wave, we are proud to launch our brand new wPhone 2, starting at just $500. our CEO mr. swope is very proud of our teams hard work.';
                $report2 = 'in our last quarter, we made a profit of $340 per wPhone and $20 per software package. our stock price also grew 3.1%.';
                $report3 = 'employee complaint #687517 : mr. John has been acting up again. i cannot believe he makes $100000 a year. fire him. now!';
                $report4 = 'these questions took a lot of work to write. mr. Swope was very helpful in writing these questions. if we had charged $20 per student we could have made lots of money.';
                $report5 = 'i fed my dog named mr. Carrot carrots the other day. he loved them, so I went to the store and spent $100 to buy him some more carrots. i guess Im forcing him to be a cannibal.';
                $report6 = 'i just spend $10000 at New Wave! free wPens for everyone! wait... i think i made a poor decision. these pens barely work. they are hard to charge from my wPhone. should I return them? i dont know. By the way, this paragraph was written as a test case exclusively because it allows us to test question marks, periods, and exclamation points.';

                self::$all = array(
                    "1" => new self(
                        /* id */        1,
                        /* disp_id */   1,
                        /* divisions */ array(1),
                        /* name */      "Purchase Receipt",
                        /* points */    2,
                        /* question */  array(
                                            "Problem" => 'New Wave Computers sells four products: hard drives, software packages, wPhones and wPens. Their retail division needs a program that will quickly calculate the total cost of a purchase from one of their stores. Their pricing scheme, as explained below, is very complicated.',
                                            "Input" => 'Four integers: <code>drives</code>, <code>software</code>, <code>phones</code>, and <code>pens</code>. These values represent the quantities of hard drives, software packages, wPhones, and wPens purchased.',
                                            "Output" => 'The total cost of all of the products as a double or float value.',
                                            "Pricing Scheme" => 'See packet.'
                                        ),
                        /* sample */    array('<code>1</code>, <code>1</code>, <code>1</code>, <code>1</code>' => '<code>1021.787</code>', '<code>8</code>, <code>3</code>, <code>4</code>, <code>5</code>' => '<code>3721.2572</code>', '<code>10</code>, <code>3</code>, <code>4</code>, <code>8</code>' => '<code>4775.8565</code>', '<code>8</code>, <code>2</code>, <code>10</code>, <code>10</code>' => '<code>7811.7972</code>'),
                        /* correct */   array("1021", "3721", "4775", "7811", "48570", "2176", "29829"),
                        /* java */      array(
                                            "stub" =>   "public static double getCost(int drives, int software, int phones, int pens) {\n\t\n}",
                                            "tests" =>  'System.out.println(getCost(1, 1, 1, 1));\nSystem.out.println(getCost(8, 3, 4, 5));\nSystem.out.println(getCost(10, 3, 4, 8));\nSystem.out.println(getCost(8, 2, 10, 10));\nSystem.out.println(getCost(12, 4, 18, 127));\nSystem.out.println(getCost(1, 1, 2, 3));\nSystem.out.println(getCost(21, 22, 23, 59));\n'
                                        ),
                        /* python */    array(
                                            "stub" =>   "def get_cost(drives, software, phones, pens):\n\t",
                                            "tests" =>  'print(get_cost(1, 1, 1, 1))\nprint(get_cost(8, 3, 4, 5))\nprint(get_cost(10, 3, 4, 8))\nprint(get_cost(8, 2, 10, 10))\nprint(get_cost(12, 4, 18, 127))\nprint(get_cost(1, 1, 2, 3))\nprint(get_cost(21, 22, 23, 59))\n'
                                        ),
                        /* C++ */       array(
                                            "stub" =>   "double getCost(int drives, int software, int phones, int pens) {\n\t\n}",
                                            "tests" =>  'cout << getCost(1, 1, 1, 1) << endl;\ncout << getCost(8, 3, 4, 5) << endl;\ncout << getCost(10, 3, 4, 8) << endl;\ncout << getCost(8, 2, 10, 10) << endl;\ncout << getCost(12, 4, 18, 127) << endl;\ncout << getCost(1, 1, 2, 3) << endl;\ncout << getCost(21, 22, 23, 59) << endl;\n'
                                        ),
                        /* hand_gr */   false,
                        /* case_sen */  false,
                        /* sort */      false,
                        /* truncate */  true
                    ),
                    "2" => new self(
                        /* id */        2,
                        /* disp_id */   2,
                        /* divisions */ array(1),
                        /* name */      "New Wave Name",
                        /* points */    2,
                        /* question */  array(
                                            "Problem" => 'New Wave Computers\' marketing department would like to display the company’s name on their wPhones and other small devices. You have been asked to write a program that will display arbitrary-length versions of their name for these small-screen devices.',
                                            "Input" => 'An integer, <code>n</code>, representing the number of characters of the name that are needed.',
                                            "Output" => 'n characters of the string “New Wave”. If n is greater than the length of the string “New Wave”, you must print the string, then a space, then the remaining characters. Note that the space counts as a character.  Output should not end with a space.  When the integer value, n, falls on a space, you should instead print a string of length n-1.',
                                            "Pricing Scheme" => 'See packet.'
                                        ),
                        /* sample */    array('<code>2</code>' => 'Ne', '<code>9</code>' => 'New Wave', '<code>10</code>' => 'New Wave N', '<code>25</code>' => 'New Wave New Wave New Wav'),
                        /* correct */   array("Ne", "New Wave", "New Wave N", "New Wave New Wave New Wav", "New W", "New Wav", "New Wave New"),
                        /* java */      array(
                                            "stub" =>   "public static String getName(int n) {\n\t\n}",
                                            "tests" =>  'System.out.println(getName(2));\nSystem.out.println(getName(9));\nSystem.out.println(getName(10));\nSystem.out.println(getName(25));\nSystem.out.println(getName(5));\nSystem.out.println(getName(7));\nSystem.out.println(getName(13));\n'
                                        ),
                        /* python */    array(
                                            "stub" =>   "def get_name(n):\n\t",
                                            "tests" =>  'print(get_name(2))\nprint(get_name(9))\nprint(get_name(10))\nprint(get_name(25))\nprint(get_name(5))\nprint(get_name(7))\nprint(get_name(13))\n'
                                        ),
                        /* C++ */       array(
                                            "stub" =>   "string getName(int n) {\n\t\n}",
                                            "tests" =>  'cout << getName(2) << endl;\ncout << getName(9) << endl;\ncout << getName(10) << endl;\ncout << getName(25) << endl;\ncout << getName(5) << endl;\ncout << getName(7) << endl;\ncout << getName(13) << endl;\n'
                                        )
                    ),
                    "3" => new self(
                        /* id */        3,
                        /* disp_id */   3,
                        /* divisions */ array(1),
                        /* name */      "Employee ID Verifier",
                        /* points */    3,
                        /* question */  array(
                                            "Problem" => 'Due to a recent security breach, New Wave Computers has implemented a new staff identification system. They need help writing a program to validate IDs and then map valid IDs to a four letter key.',
                                            "Input" => 'An 8-digit integer, <code>id</code>, representing an employee’s ID.',
                                            "Output" => 'If the 8-digit integer ID does not match New Wave’s prescribed security algorithm you should return the string “invalid”. If the ID is valid then you should return the 8-digit ID converted to a four character string.',
                                            "Algorithm" => 'See packet.'
                                        ),
                        /* sample */    array('<code>23456789</code>' => 'invalid', '<code>12759745</code>' => 'invalid', '<code>73294717</code>' => 'invalid', '<code>25354565</code>' => 'yism', '<code>25432167</code>' => 'yquo'),
                        /* correct */   array("invalid", "invalid", "invalid", "yism", "yquo", "koko", "iiii", "qqqq", "invalid", "invalid"),
                        /* java */      array(
                                            "stub" =>   "public static String validateID(int id) {\n\t\n}",
                                            "tests" =>  'System.out.println(validateID(23456789));\nSystem.out.println(validateID(12759745));\nSystem.out.println(validateID(73294717));\nSystem.out.println(validateID(25354565));\nSystem.out.println(validateID(25432167));\nSystem.out.println(validateID(89678967));\nSystem.out.println(validateID(87878787));\nSystem.out.println(validateID(69696969));\nSystem.out.println(validateID(24567890));\nSystem.out.println(validateID(25272923));\n'
                                        ),
                        /* python */    array(
                                            "stub" =>   "def validate_id(id):\n\t",
                                            "tests" =>  'print(validate_id(23456789))\nprint(validate_id(12759745))\nprint(validate_id(73294717))\nprint(validate_id(25354565))\nprint(validate_id(25432167))\nprint(validate_id(89678967))\nprint(validate_id(87878787))\nprint(validate_id(69696969))\nprint(validate_id(24567890))\nprint(validate_id(25272923))\n'
                                        ),
                        /* C++ */       array(
                                            "stub" =>   "string validateID(int id) {\n\t\n}",
                                            "tests" =>  'cout << validateID(23456789) << endl;\ncout << validateID(12759745) << endl;\ncout << validateID(73294717) << endl;\ncout << validateID(25354565) << endl;\ncout << validateID(25432167) << endl;\ncout << validateID(89678967) << endl;\ncout << validateID(87878787) << endl;\ncout << validateID(69696969) << endl;\ncout << validateID(24567890) << endl;\ncout << validateID(25272923) << endl;\n'
                                        )
                    ),
                    "4" => new self(
                        /* id */        4,
                        /* disp_id */   4,
                        /* divisions */ array(1),
                        /* name */      "Loading Symbol",
                        /* points */    3,
                        /* question */  array(
                                            "Problem" => 'You are designing an hourglass loading symbol for New Wave’s new patent-pending word processor. This symbol will consist of asterisks. The loading icon needs to be resizeable in order to work well on screens of different sizes.',
                                            "Input" => 'An integer, <code>n</code>, representing the number of rows the hourglass should have.',
                                            "Output" => 'If <code>n</code> is odd, the first row should be n asterisks long, with each succeeding row having 2 fewer asterisks with spaces to center it until there is only 1 asterisk, at which point the pattern is reversed until there are n asterisks again. There will only be one row that has a single asterisk.<br><br>If <code>n</code> is even, the first row should be n-1 asterisks long, with each succeeding row having 2 fewer asterisks with spaces to center it until there is only 1 asterisk, at which point the pattern is reversed until there are n-1 asterisks again. There will be exactly two rows that have a single asterisk.',
                                            "Note" => 'There should not be any spaces between asterisks. Your output for this problem will be printed to the console. Your method will not return anything; It is a void method.'
                                        ),
                        /* sample */    array('<code>1</code>' => '*', '<code>2</code>' => '*<br>*', '<code>3</code>' => '***<br> *<br>***', '<code>4</code>' => '***<br> *<br> *<br>***', '<code>5</code>' => '*****<br> ***<br>  *<br> ***<br>*****', '<code>6</code>' => '*****<br> ***<br>  *<br>  *<br> ***<br>*****'),
                        /* correct */   array(
                                            "*",
                                            "*", "*",
                                            "***", " *", "***",
                                            "***", " *", " *", "***",
                                            "*****", " ***", "  *", " ***", "*****",
                                            "*****", " ***", "  *", "  *", " ***", "*****",
                                            "*******", " *****", "  ***", "   *", "  ***", " *****", "*******",
                                            "*******", " *****", "  ***", "   *", "   *", "  ***", " *****", "*******",
                                            "*********", " *******", "  *****", "   ***", "    *", "   ***", "  *****", " *******", "*********",
                                        ),
                        /* java */      array(
                                            "stub" =>   "public static void printHourglass(int n) {\n\t\n}",
                                            "tests" =>  'printHourglass(1);\nprintHourglass(2);\nprintHourglass(3);\nprintHourglass(4);\nprintHourglass(5);\nprintHourglass(6);\nprintHourglass(7);\nprintHourglass(8);\nprintHourglass(9);\n'
                                        ),
                        /* python */    array(
                                            "stub" =>   "def print_hourglass(n):\n\t",
                                            "tests" =>  'print_hourglass(1)\nprint_hourglass(2)\nprint_hourglass(3)\nprint_hourglass(4)\nprint_hourglass(5)\nprint_hourglass(6)\nprint_hourglass(7)\nprint_hourglass(8)\nprint_hourglass(9)\n'
                                        ),
                        /* C++ */       array(
                                            "stub" =>   "void printHourglass(int n) {\n\t\n}",
                                            "tests" =>  'printHourglass(1);\nprintHourglass(2);\nprintHourglass(3);\nprintHourglass(4);\nprintHourglass(5);\nprintHourglass(6);\nprintHourglass(7);\nprintHourglass(8);\nprintHourglass(9);\n'
                                        )
                    ),
                    "5" => new self(
                        /* id */        5,
                        /* disp_id */   5,
                        /* divisions */ array(1),
                        /* name */      "Barcode",
                        /* points */    4,
                        /* question */  array(
                                            "Problem" => 'New Wave Computers places a six digit barcode on each of their products that looks like the following:<br><center>|:|::  :|:|:   ||:::   :::||  :|::|  :::||</center><br>Each digit in the barcode is comprised of full bars ‘|’ and half bars ‘:’. Each digit is separated by a single space. <br><br>To decode a digit, convert each full bar to a 1 and each half bar to a 0. Then, use the table in the packet to turn the sequences of 0s and 1s into an integer.<br><br>Note that they represent all combinations of two full and three half bars. The digit can be computed easily from the bar code using the column weights 7, 4, 2, 1, 0.  For example, 01100 is<br><br><center>0 * 7 + 1 * 4 + 1 * 2 + 0 * 1 + 0 * 0 = 6</center><br>The only exception is 0, which would yield 11 according to the weight formula.<br><br>The first five encoded digits are followed by a check digit, which is computed as follows: Add up the first five digits, and choose the check digit to make this sum a multiple of 10.  For example, the sum of the digits in the bar code 95014 is 19, so the check digit is 1, to make the sum equal to 20.  Write a method that accepts a barcode as input and returns whether or not the barcode’s check digit is valid.',
                                            "Input" => 'String barcode - A 39 character String of \'|\'s, \':\'s and spaces.',
                                            "Output" => 'A boolean value which is true if the barcode’s check digit is valid and false if the barcode’s check digit is not valid.',
                                        ),
                        /* sample */    array('|:::| ::||: |::|: :::|| ||::: :|:|:' => '<code>false</code>', ':::|| :|:|: |::|: :|::| ::|:| ::||:' => '<code>false</code>', '||::: |:::| :||:: :|:|: :::|| :::||' => '<code>true</code>'),
                        /* correct */   array("false", "false", "true", "true", "false", "false", "true"),
                        /* java */      array(
                                            "stub" =>   "public static boolean isValidBarcode(String barcode) {\n\t\n}",
                                            "tests" =>  'System.out.println(isValidBarcode("|:::| ::||: |::|: :::|| ||::: :|:|:"));\nSystem.out.println(isValidBarcode(":::|| :|:|: |::|: :|::| ::|:| ::||:"));\nSystem.out.println(isValidBarcode("||::: |:::| :||:: :|:|: :::|| :::||"));\nSystem.out.println(isValidBarcode("|:|:: |:::| ||::: :||:: |::|: ||:::"));\nSystem.out.println(isValidBarcode("|::|: |:|:: ::|:| :||:: :||:: ||:::"));\nSystem.out.println(isValidBarcode(":::|| :|:|: :::|| :|::| :||:: :::||"));\nSystem.out.println(isValidBarcode(":::|| :|:|: :::|| :|::| :||:: ::||:"));\n'
                                        ),
                        /* python */    array(
                                            "stub" =>   "def is_valid_barcode(barcode):\n\t",
                                            "tests" =>  'print(is_valid_barcode("|:::| ::||: |::|: :::|| ||::: :|:|:"))\nprint(is_valid_barcode(":::|| :|:|: |::|: :|::| ::|:| ::||:"))\nprint(is_valid_barcode("||::: |:::| :||:: :|:|: :::|| :::||"))\nprint(is_valid_barcode("|:|:: |:::| ||::: :||:: |::|: ||:::"))\nprint(is_valid_barcode("|::|: |:|:: ::|:| :||:: :||:: ||:::"))\nprint(is_valid_barcode(":::|| :|:|: :::|| :|::| :||:: :::||"))\nprint(is_valid_barcode(":::|| :|:|: :::|| :|::| :||:: ::||:"))\n'
                                        ),
                        /* C++ */       array(
                                            "stub" =>   "bool isValidBarcode(string barcode) {\n\t\n}",
                                            "tests" =>  'cout << isValidBarcode("|:::| ::||: |::|: :::|| ||::: :|:|:") << endl;\ncout << isValidBarcode(":::|| :|:|: |::|: :|::| ::|:| ::||:") << endl;\ncout << isValidBarcode("||::: |:::| :||:: :|:|: :::|| :::||") << endl;\ncout << isValidBarcode("|:|:: |:::| ||::: :||:: |::|: ||:::") << endl;\ncout << isValidBarcode("|::|: |:|:: ::|:| :||:: :||:: ||:::") << endl;\ncout << isValidBarcode(":::|| :|:|: :::|| :|::| :||:: :::||") << endl;\ncout << isValidBarcode(":::|| :|:|: :::|| :|::| :||:: ::||:") << endl;\n'
                                        )
                    ),
                    "6" => new self(
                        /* id */        6,
                        /* disp_id */   6,
                        /* divisions */ array(1),
                        /* name */      "Weekly Report",
                        /* points */    4,
                        /* question */  array(
                                            "Problem" => 'Due to recent inconsistencies observed by the SEC, New Wave must release weekly earnings reports.  Unfortunately these reports often contain several mistakes. The SEC also requests that all monetary values be converted to euros so they can better analyze New Wave’s oversea transactions.',
                                            "Input" => 'A string, <code>report</code>, containing the full report.',
                                            "Output" => 'The same report, but with the following modifications made:<ul><li>The first letter of every sentence must be a capital letter, including the first sentence in the report. A sentence can end with either a period, a question mark, or an exclamation point. There will always be exactly one space between a punctuation mark and the first letter of the next sentence.</li><li>In all instances of “mr”, “ms”, or “mrs”, the m should be capitalized. All instances of these titles will be followed with a period and a space.</li><li>All dollar values should be converted to euro values. A dollar value is a dollar sign followed by one or more integer digits with no spaces. The dollar sign should be replaced with the letter E, and the value should be converted based on the equation euro = .75 * dollar. All numeric values greater than 999 will be written without commas and all dollar values will be whole numbers.</li></ul>',
                                        ),
                        /* sample */    array(
                                          $report1 => "Here at New Wave, we are proud to launch our brand new wPhone 2, starting at just E375. Our CEO Mr. Swope is very proud of our teams hard work.",
                                          $report2 => "In our last quarter, we made a profit of E255 per wPhone and E15 per software package. Our stock price also grew 3.1%.",
                                          $report3 => "Employee complaint #687517 : Mr. John has been acting up again. I cannot believe he makes E75000 a year. Fire him. Now!"
                                        ),
                        /* correct */   array(
                                            "Here at New Wave, we are proud to launch our brand new wPhone 2, starting at just E375. Our CEO Mr. Swope is very proud of our teams hard work.",
                                            "In our last quarter, we made a profit of E255 per wPhone and E15 per software package. Our stock price also grew 3.1%.",
                                            "Employee complaint #687517 : Mr. John has been acting up again. I cannot believe he makes E75000 a year. Fire him. Now!",
                                            "These questions took a lot of work to write. Mr. Swope was very helpful in writing these questions. If we had charged E15 per student we could have made lots of money.",
                                            "I fed my dog named Mr. Carrot carrots the other day. He loved them, so I went to the store and spent E75 to buy him some more carrots. I guess Im forcing him to be a cannibal.",
                                            "I just spend E7500 at New Wave! Free wPens for everyone! Wait... I think i made a poor decision. These pens barely work. They are hard to charge from my wPhone. Should I return them? I dont know. By the way, this paragraph was written as a test case exclusively because it allows us to test question marks, periods, and exclamation points."
                                        ),
                        /* java */      array(
                                            "stub" =>   "public static String fixReport(String report) {\n\t\n}",
                                            "tests" =>  'System.out.println(fixReport("' . $report1 . '"));\nSystem.out.println(fixReport("' . $report2 . '"));\nSystem.out.println(fixReport("' . $report3 . '"));\nSystem.out.println(fixReport("' . $report4 . '"));\nSystem.out.println(fixReport("' . $report5 . '"));\nSystem.out.println(fixReport("' . $report6 . '"));\n'
                                        ),
                        /* python */    array(
                                            "stub" =>   "def fix_report(report):\n\t",
                                            "tests" =>  'print(fix_report("' . $report1 . '"))\nprint(fix_report("' . $report2 . '"))\nprint(fix_report("' . $report3 . '"))\nprint(fix_report("' . $report4 . '"))\nprint(fix_report("' . $report5 . '"))\nprint(fix_report("' . $report6 . '"))\n'
                                        ),
                        /* C++ */       array(
                                            "stub" =>   "string fixReport(string report) {\n\t\n}",
                                            "tests" =>  'cout << fixReport("' . $report1 . '") << endl;\ncout << fixReport("' . $report2 . '") << endl;\ncout << fixReport("' . $report3 . '") << endl;\ncout << fixReport("' . $report4 . '") << endl;\ncout << fixReport("' . $report5 . '") << endl;\ncout << fixReport("' . $report6 . '") << endl;\n'
                                        ),
                        /* hand_gr */   false,
                        /* case_sens */ true
                    ),
                    "7" => new self(
                        /* id */        7,
                        /* disp_id */   7,
                        /* divisions */ array(1),
                        /* name */      "Diecisiete",
                        /* points */    "up to 8",
                        /* question */  array(
                                            "Problem" => 'New Wave Computers’ Gaming Division’s newest release will be the game of Diecisiete.  This game is played using a modified Uno deck.  The object of the game of Diecisiete is to beat the computer in one of the following ways: reach a final score higher than the computer without exceeding 17 or be dealt 5 cards without exceeding a value of 17.',
                                            "Rules" => 'At the start of the game the player and computer are each dealt two cards.  The computer and player take turns throughout the game either drawing another card or staying.  Once the computer or player has decided to ‘stay’ they can no longer ‘draw’ additional cards. Play will continue until the the other contestant either ‘stays’, is dealt five cards or exceeds a sum of 17.  The player will be given the option to either stay or draw throughout the game.  The computer should continue to draw until it’s hand has a total greater than 13 or it can see that the player has exceeded 17.  The computer cannot ‘see’ the first card in the player’s hand so the summation that the computer uses to to determine if it should draw or stay should not take this first card into account.<br><br>The modified Uno deck contains 80 cards. There are four suits: red, green, yellow and blue.  Each color consists of one 0 card, two 1s, two 2s, two 3s, two 4s, two 5s, two 6s, two 7s, two 8s and two 9s. The player\'s and dealer’s score is calculated by summing the values of each card in their hands.  When calculating this sum each rank card has a value equal to its rank.<br><br>During game play you cannot see the dealer’s first card but will be able to see any card after this.  Cards should be randomly dealt.  You do not need to keep track of which cards have already been dealt, but should instead generate a random number between 0 and 3 for the card’s color (0 – red, 1 - green, 2 - blue, 3 – yellow) and a random number between 0 and 9 for it’s value.  Zero through nine represent a card with ranks from zero to nine.',
                                            "Note" => 'This problem will be graded by a real-life developer. The rubric by which it will be graded is at the back of this packet. Note that a non-working or partially-working project can still receive points for design and organization.',
                                        ),
                        /* sample */    'Welcome to Diecisiete.<br><br>Computer’s hand: * R4<br><br>Your hand: Y5 G3<br><br>The computer has chosen to draw another card.<br><br>Computer’s hand: * R4 Y2<br><br>Your hand: Y5 G3<br><br>Enter a 1 to draw or a 2 to stay<br><br>1<br><br>Computer’s hand: * R4 Y2<br><br>Your hand: Y5 G3 G7<br><br>The computer has chosen to stay.<br><br>Computer’s hand: * R4 Y2<br><br>Your hand: Y5 G3 G7<br><br>Enter a 1 to draw or a 2 to stay<br><br>2<br><br>Computer’s hand: B9 R4 Y2<br><br>Your hand: Y5 G3 G7<br><br>The computer’s total is 15.  Your total is 15.  The computer wins.',
                        /* correct */   null,
                        /* java */      null,
                        /* python */    null,
                        /* C++ */       null,
                        /* hand_gr */   true
                    ),
                    "8" => new self(
                        /* id */        8,
                        /* disp_id */   1,
                        /* divisions */ array(2),
                        /* name */      "Loading Symbol",
                        /* points */    2,
                        /* question */  array(
                                            "Problem" => 'You are designing an hourglass loading symbol for New Wave’s new patent-pending word processor. Your loading icon needs to be resizeable in order to work well on screens of different sizes.',
                                            "Input" => 'An integer, <code>n</code>, representing the number of rows the hourglass should have.',
                                            "Output" => 'An hourglass pattern made with n rows as per the samples below where each row is made out of the first m needed characters of the string “NewWave”.  If a row has more than 8 characters you should repeat the characters in the string “NewWave” as needed.<br><br>If n is odd, the first row should be n characters long, with each succeeding row having 2 fewer characters  with spaces to center it until there is only 1 character, at which point the pattern is reversed until there are n characters  again. There will only be one row that has a single characters .<br><br>If n is even, the first row should be n-1 characters long, with each succeeding row having 2 fewer characters with spaces to center it until there is only 1 character, at which point the pattern is reversed until there are n-1 characters again. There will be exactly two rows that have a single character.',
                                            "Note" => 'There should not be any spaces between characters. Your output for this problem will be printed to the console. Your method will not return anything; It is a void method.'
                                        ),
                        /* sample */    array('<code>1</code>' => 'N', '<code>2</code>' => 'N<br>N', '<code>3</code>' => 'New<br> N<br>New', '<code>4</code>' => 'New<br> N<br> N<br>New', '<code>5</code>' => 'NewWa<br> New<br>  N<br> New<br>NewWa', '<code>10</code>' => 'NewWaveNe<br> NewWave<br>  NewWa<br>   New<br>    N<br>    N<br>   New<br>  NewWa<br> NewWave<br>NewWaveNe'),
                        /* correct */   array(
                                            "N",
                                            "N", "N",
                                            "New", " N", "New",
                                            "New", " N", " N", "New",
                                            "NewWa", " New", "  N", " New", "NewWa",
                                            "NewWaveNe", " NewWave", "  NewWa", "   New", "    N", "    N", "   New", "  NewWa", " NewWave", "NewWaveNe",
                                            "NewWa", " New", "  N", "  N", " New", "NewWa",
                                            "NewWave", " NewWa", "  New", "   N", "   N", "  New", " NewWa", "NewWave",
                                            "NewWaveNe", " NewWave", "  NewWa", "   New", "    N", "   New", "  NewWa", " NewWave", "NewWaveNe"
                                        ),
                        /* java */      array(
                                            "stub" =>   "public static void printHourglass(int n) {\n\t\n}",
                                            "tests" =>  'printHourglass(1);\nprintHourglass(2);\nprintHourglass(3);\nprintHourglass(4);\nprintHourglass(5);\nprintHourglass(10);\nprintHourglass(6);\nprintHourglass(8);\nprintHourglass(9);\n'
                                        ),
                        /* python */    array(
                                            "stub" =>   "def print_hourglass(n):\n\t",
                                            "tests" =>  'print_hourglass(1)\nprint_hourglass(2)\nprint_hourglass(3)\nprint_hourglass(4)\nprint_hourglass(5)\nprint_hourglass(10)\nprint_hourglass(6)\nprint_hourglass(8)\nprint_hourglass(9)\n'
                                        ),
                        /* C++ */       array(
                                            "stub" =>   "void printHourglass(int n) {\n\t\n}",
                                            "tests" =>  'printHourglass(1);\nprintHourglass(2);\nprintHourglass(3);\nprintHourglass(4);\nprintHourglass(5);\nprintHourglass(10);\nprintHourglass(6);\nprintHourglass(8);\nprintHourglass(9);\n'
                                        )
                    ),
                    "9" => new self(
                        /* id */        9,
                        /* disp_id */   2,
                        /* divisions */ array(2),
                        /* name */      "Weekly Report",
                        /* points */    2,
                        /* question */  array(
                                            "Problem" => 'Due to recent inconsistencies observed by the SEC, New Wave must release weekly earnings reports.  Unfortunately these reports often contain several mistakes. The SEC also requests that all monetary values be converted to euros so they can better analyze New Wave’s oversea transactions.',
                                            "Input" => 'A string, <code>report</code>, containing the full report.',
                                            "Output" => 'The same report, but with the following modifications made:<ul><li>The first letter of every sentence must be a capital letter, including the first sentence in the report. A sentence can end with either a period, a question mark, or an exclamation point. There will always be exactly one space between a punctuation mark and the first letter of the next sentence.</li><li>In all instances of “mr”, “ms”, or “mrs”, the m should be capitalized. All instances of these titles will be followed with a period and a space.</li><li>All dollar values should be converted to euro values. A dollar value is a dollar sign followed by one or more integer digits with no spaces. The dollar sign should be replaced with a euro sign (€), and the value should be converted based on the equation euro = .75 * dollar. All numeric values greater than 999 will be written without commas and all dollar values will be whole numbers.</li></ul>',
                                        ),
                        /* sample */    array(
                                          $report1 => "Here at New Wave, we are proud to launch our brand new wPhone 2, starting at just E375. Our CEO Mr. Swope is very proud of our teams hard work.",
                                          $report2 => "In our last quarter, we made a profit of E255 per wPhone and E15 per software package. Our stock price also grew 3.1%.",
                                          $report3 => "Employee complaint #687517 : Mr. John has been acting up again. I cannot believe he makes E75000 a year. Fire him. Now!"
                                        ),
                        /* correct */   array(
                                            "Here at New Wave, we are proud to launch our brand new wPhone 2, starting at just E375. Our CEO Mr. Swope is very proud of our teams hard work.",
                                            "In our last quarter, we made a profit of E255 per wPhone and E15 per software package. Our stock price also grew 3.1%.",
                                            "Employee complaint #687517 : Mr. John has been acting up again. I cannot believe he makes E75000 a year. Fire him. Now!",
                                            "These questions took a lot of work to write. Mr. Swope was very helpful in writing these questions. If we had charged E15 per student we could have made lots of money.",
                                            "I fed my dog named Mr. Carrot carrots the other day. He loved them, so I went to the store and spent E75 to buy him some more carrots. I guess Im forcing him to be a cannibal.",
                                            "I just spend E7500 at New Wave! Free wPens for everyone! Wait... I think i made a poor decision. These pens barely work. They are hard to charge from my wPhone. Should I return them? I dont know. By the way, this paragraph was written as a test case exclusively because it allows us to test question marks, periods, and exclamation points."
                                        ),
                        /* java */      array(
                                            "stub" =>   "public static String fixReport(String report) {\n\t\n}",
                                            "tests" =>  'System.out.println(fixReport("' . $report1 . '"));\nSystem.out.println(fixReport("' . $report2 . '"));\nSystem.out.println(fixReport("' . $report3 . '"));\nSystem.out.println(fixReport("' . $report4 . '"));\nSystem.out.println(fixReport("' . $report5 . '"));\nSystem.out.println(fixReport("' . $report6 . '"));\n'
                                        ),
                        /* python */    array(
                                            "stub" =>   "def fix_report(report):\n\t",
                                            "tests" =>  'print(fix_report("' . $report1 . '"))\nprint(fix_report("' . $report2 . '"))\nprint(fix_report("' . $report3 . '"))\nprint(fix_report("' . $report4 . '"))\nprint(fix_report("' . $report5 . '"))\nprint(fix_report("' . $report6 . '"))\n'
                                        ),
                        /* C++ */       array(
                                            "stub" =>   "string fixReport(string report) {\n\t\n}",
                                            "tests" =>  'cout << fixReport("' . $report1 . '") << endl;\ncout << fixReport("' . $report2 . '") << endl;\ncout << fixReport("' . $report3 . '") << endl;\ncout << fixReport("' . $report4 . '") << endl;\ncout << fixReport("' . $report5 . '") << endl;\ncout << fixReport("' . $report6 . '") << endl;\n'
                                        ),
                        /* hand_gr */   false,
                        /* case_sens */ true
                    ),
                    "10" => new self(
                        /* id */        10,
                        /* disp_id */   3,
                        /* divisions */ array(2),
                        /* name */      "Yacht Sea",
                        /* points */    3,
                        /* question */  array(
                                            "Problem" => 'At New Wave Computers’ annual picnic, employees compete in a dice game called Yacht Sea. The game is played by rolling five dice and then fill in a table based on these values that have been rolled. You will write a program that will determine the highest score that can be achieved with a single roll.',
                                            "Input" => 'An array of five integers, <code>roll</code>.',
                                            "Output" => 'An integer value that is the highest score that can be achieved from the roll based on the table in the packet.',
                                        ),
                        /* sample */    array(
                                            '<code>{6, 2, 6, 6, 4}</code>' => '<code>25</code>',
                                            '<code>{1, 4, 6, 4, 1}</code>' => '<code>8</code>',
                                            '<code>{1, 2, 4, 3, 2}</code>' => '<code>30</code>',
                                            '<code>{1, 1, 1, 1, 1}</code>' => '<code>50</code>',
                                            '<code>{2, 2, 5, 3, 1}</code>' => '<code>5</code>',
                                        ),
                        /* correct */   array("25", "8", "30", "50", "5", "30", "3", "6", "25", "50"),
                        /* java */      array(
                                            "stub" =>   "public static int getMaxValue(int[] roll) {\n\t\n}",
                                            "tests" =>  'System.out.println(getMaxValue(new int[] {6, 2, 6, 6, 2}));\nSystem.out.println(getMaxValue(new int[] {1, 4, 6, 4, 1}));\nSystem.out.println(getMaxValue(new int[] {1, 2, 4, 3, 2}));\nSystem.out.println(getMaxValue(new int[] {1, 1, 1, 1, 1}));\nSystem.out.println(getMaxValue(new int[] {2, 2, 5, 3, 1}));\nSystem.out.println(getMaxValue(new int[] {2, 4, 5, 3, 2}));\nSystem.out.println(getMaxValue(new int[] {1, 2, 3, 1, 1}));\nSystem.out.println(getMaxValue(new int[] {6, 2, 5, 2, 4}));\nSystem.out.println(getMaxValue(new int[] {5, 6, 6, 5, 5}));\nSystem.out.println(getMaxValue(new int[] {6, 6, 6, 6, 6}));\n'
                                        ),
                        /* python */    array(
                                            "stub" =>   "def get_max_value(roll):\n\t",
                                            "tests" =>  'print(get_max_value([6, 2, 6, 6, 2]))\nprint(get_max_value([1, 4, 6, 4, 1]))\nprint(get_max_value([1, 2, 4, 3, 2]))\nprint(get_max_value([1, 1, 1, 1, 1]))\nprint(get_max_value([2, 2, 5, 3, 1]))\nprint(get_max_value([2, 4, 5, 3, 2]))\nprint(get_max_value([1, 2, 3, 1, 1]))\nprint(get_max_value([6, 2, 5, 2, 4]))\nprint(get_max_value([5, 6, 6, 5, 5]))\nprint(get_max_value([6, 6, 6, 6, 6]))\n'
                                        ),
                        /* C++ */       array(
                                            "stub" =>   "int getMaxValue(int roll[]) {\n\t\n}",
                                            "tests" =>  'int values[] = {6, 2, 6, 6, 2}; cout << getMaxValue(values) << endl;\nvalues = {1, 4, 6, 4, 1}; cout << getMaxValue(values) << endl;\nvalues = {1, 2, 4, 3, 2}; cout << getMaxValue(values) << endl;\nvalues = {1, 1, 1, 1, 1}; cout << getMaxValue(values) << endl;\nvalues = {2, 2, 5, 3, 1}; cout << getMaxValue(values) << endl;\nvalues = {2, 4, 5, 3, 2}; cout << getMaxValue(values) << endl;\nvalues = {1, 2, 3, 1, 1}; cout << getMaxValue(values) << endl;\nvalues = {6, 2, 5, 2, 4}; cout << getMaxValue(values) << endl;\nvalues = {5, 6, 6, 5, 5}; cout << getMaxValue(values) << endl;\nvalues = {6, 6, 6, 6, 6}; cout << getMaxValue(values) << endl;\n'
                                        )
                    ),
                    "11" => new self(
                        /* id */        11,
                        /* disp_id */   4,
                        /* divisions */ array(2),
                        /* name */      "Newdoku",
                        /* points */    3,
                        /* question */  array(
                                            "Problem" => 'A few New Wave employees plan to enter a programming competition. Each competitor brings a Sudoku solver and the solvers compete to see which can solve the most challenging Sudoku boards the fastest. They need a little help making sure the solutions from their solvers are correct.',
                                            "Input" => 'A two-dimensional 9x9 array of integers, <code>board</code>, representing a board state.',
                                            "Output" => 'An integer value, representing the number of total errors on the board. An error occurs in any row (left to right), column (up and down), or box (3x3 square) if the row, column, or box does not contain the digits 1-9, each exactly once.',
                                        ),
                        /* sample */    array(
                                            '<code>{<br>{5, 3, 4, 6, 7, 8, 9, 1, 2},<br>{6, 7, 2, 1, 9, 5, 3, 4, 8},<br>{1, 9, 8, 3, 4, 2, 5, 6, 7},<br>{8, 5, 9, 7, 6, 1, 4, 2, 3},<br>{4, 2, 6, 8, 5, 3, 7, 9, 1},<br>{7, 1, 3, 9, 2, 4, 8, 5, 6},<br>{9, 6, 1, 5, 3, 7, 2, 8, 4},<br>{2, 8, 7, 4, 1, 9, 6, 3, 5},<br>{3, 4, 5, 2, 8, 6, 1, 7, 9}<br>};</code>' => '<code>0</code>',
                                            '<code>{<br>{1, 7, 5, 8, 3, 9, 4, 2, 6},<br>{6, 3, 6, 2, 7, 4, 9, 1, 5},<br>{4, 2, 9, 6, 5, 1, 3, 7, 8},<br>{8, 1, 8, 3, 9, 5, 7, 4, 2},<br>{5, 4, 7, 1, 6, 2, 8, 3, 9},<br>{2, 9, 3, 4, 8, 7, 6, 5, 1},<br>{7, 5, 4, 9, 2, 6, 1, 8, 3},<br>{9, 8, 1, 5, 4, 3, 2, 6, 7},<br>{3, 6, 2, 7, 1, 8, 5, 9, 4}<br>};</code>' => '<code>4</code>',
                                            '<code>{<br>{5, 3, 4, 6, 7, 8, 9, 1, 2},<br>{2, 7, 6, 1, 9, 5, 3, 4, 8},<br>{1, 9, 8, 3, 4, 2, 5, 6, 7},<br>{8, 5, 9, 7, 6, 1, 4, 2, 3},<br>{4, 2, 6, 8, 5, 3, 7, 9, 1},<br>{7, 1, 3, 9, 2, 4, 8, 5, 6},<br>{9, 6, 1, 5, 3, 7, 2, 8, 4},<br>{2, 8, 7, 4, 1, 9, 6, 3, 5},<br>{3, 4, 5, 2, 8, 6, 1, 7, 9}<br>};</code>' => '<code>2</code>',
                                            '<code>{<br>{9, 5, 3, 2, 1, 4, 7, 6, 8},<br>{2, 7, 6, 8, 5, 3, 4, 1, 9},<br>{8, 1, 4, 6, 7, 9, 5, 3, 2},<br>{7, 4, 8, 5, 3, 1, 6, 9, 2},<br>{6, 9, 1, 7, 4, 5, 2, 8, 3},<br>{5, 3, 2, 9, 6, 8, 1, 7, 4},<br>{1, 6, 9, 4, 8, 5, 3, 2, 7},<br>{3, 2, 5, 1, 9, 7, 8, 4, 6},<br>{8, 4, 7, 3, 2, 6, 9, 5, 1}<br>};</code>' => '<code>6</code>',
                                        ),
                        /* correct */   array("0", "4", "2", "6", "0", "3", "2"),
                        /* java */      array(
                                            "stub" =>   "public static int findErrors(int[][] board) {\n\t\n}",
                                            "tests" =>  'System.out.println(findErrors(new int[][] {{5, 3, 4, 6, 7, 8, 9, 1, 2},{6, 7, 2, 1, 9, 5, 3, 4, 8},{1, 9, 8, 3, 4, 2, 5, 6, 7},{8, 5, 9, 7, 6, 1, 4, 2, 3},{4, 2, 6, 8, 5, 3, 7, 9, 1},{7, 1, 3, 9, 2, 4, 8, 5, 6},{9, 6, 1, 5, 3, 7, 2, 8, 4},{2, 8, 7, 4, 1, 9, 6, 3, 5},{3, 4, 5, 2, 8, 6, 1, 7, 9}}));\nSystem.out.println(findErrors(new int[][] {{1, 7, 5, 8, 3, 9, 4, 2, 6},{6, 3, 6, 2, 7, 4, 9, 1, 5},{4, 2, 9, 6, 5, 1, 3, 7, 8},{8, 1, 8, 3, 9, 5, 7, 4, 2},{5, 4, 7, 1, 6, 2, 8, 3, 9},{2, 9, 3, 4, 8, 7, 6, 5, 1},{7, 5, 4, 9, 2, 6, 1, 8, 3},{9, 8, 1, 5, 4, 3, 2, 6, 7},{3, 6, 2, 7, 1, 8, 5, 9, 4}}));\nSystem.out.println(findErrors(new int[][] {{5, 3, 4, 6, 7, 8, 9, 1, 2},{2, 7, 6, 1, 9, 5, 3, 4, 8},{1, 9, 8, 3, 4, 2, 5, 6, 7},{8, 5, 9, 7, 6, 1, 4, 2, 3},{4, 2, 6, 8, 5, 3, 7, 9, 1},{7, 1, 3, 9, 2, 4, 8, 5, 6},{9, 6, 1, 5, 3, 7, 2, 8, 4},{2, 8, 7, 4, 1, 9, 6, 3, 5},{3, 4, 5, 2, 8, 6, 1, 7, 9}}));\nSystem.out.println(findErrors(new int[][] {{9, 5, 3, 2, 1, 4, 7, 6, 8},{2, 7, 6, 8, 5, 3, 4, 1, 9},{8, 1, 4, 6, 7, 9, 5, 3, 2},{7, 4, 8, 5, 3, 1, 6, 9, 2},{6, 9, 1, 7, 4, 5, 2, 8, 3},{5, 3, 2, 9, 6, 8, 1, 7, 4},{1, 6, 9, 4, 8, 5, 3, 2, 7},{3, 2, 5, 1, 9, 7, 8, 4, 6},{8, 4, 7, 3, 2, 6, 9, 5, 1}}));\nSystem.out.println(findErrors(new int[][] {{9, 2, 5, 6, 3, 1, 8, 4, 7},{6, 1, 8, 5, 7, 4, 2, 9, 3},{3, 7, 4, 9, 8, 2, 5, 6, 1},{7, 4, 9, 8, 2, 6, 1, 3, 5},{8, 5, 2, 4, 1, 3, 9, 7, 6},{1, 6, 3, 7, 9, 5, 4, 8, 2},{2, 8, 7, 3, 5, 9, 6, 1, 4},{4, 9, 1, 2, 6, 7, 3, 5, 8},{5, 3, 6, 1, 4 ,8, 7, 2, 9}}));\nSystem.out.println(findErrors(new int[][] {{1, 2, 5, 6, 3, 1, 8, 4, 7},{6, 1, 8, 5, 7, 4, 2, 9, 3},{3, 7, 4, 9, 8, 2, 5, 6, 1},{7, 4, 9, 8, 2, 6, 1, 3, 5},{8, 5, 2, 4, 1, 3, 9, 7, 6},{1, 6, 3, 7, 9, 5, 4, 8, 2},{2, 8, 7, 3, 5, 9, 6, 1, 4},{4, 9, 1, 2, 6, 7, 3, 5, 8},{5, 3, 6, 1, 4 ,8, 7, 2, 9}}));\nSystem.out.println(findErrors(new int[][] {{9, 2, 5, 6, 3, 1, 8, 4, 7},{6, 1, 8, 5, 7, 4, 2, 9, 3},{3, 7, 4, 9, 8, 2, 5, 6, 1},{7, 4, 9, 8, 2, 6, 1, 3, 5},{8, 5, 2, 4, 1, 3, 9, 7, 6},{1, 6, 3, 7, 9, 5, 4, 8, 2},{2, 8, 7, 3, 5, 9, 6, 1, 4},{4, 9, 1, 6, 2, 7, 3, 5, 8},{5, 3, 6, 1, 4 ,8, 7, 2, 9}}));\n'
                                        ),
                        /* python */    array(
                                            "stub" =>   "def find_errors(board):\n\t",
                                            "tests" =>  'print(find_errors([[5, 3, 4, 6, 7, 8, 9, 1, 2],[6, 7, 2, 1, 9, 5, 3, 4, 8],[1, 9, 8, 3, 4, 2, 5, 6, 7],[8, 5, 9, 7, 6, 1, 4, 2, 3],[4, 2, 6, 8, 5, 3, 7, 9, 1],[7, 1, 3, 9, 2, 4, 8, 5, 6],[9, 6, 1, 5, 3, 7, 2, 8, 4],[2, 8, 7, 4, 1, 9, 6, 3, 5],[3, 4, 5, 2, 8, 6, 1, 7, 9]]))\nprint(find_errors([[1, 7, 5, 8, 3, 9, 4, 2, 6],[6, 3, 6, 2, 7, 4, 9, 1, 5],[4, 2, 9, 6, 5, 1, 3, 7, 8],[8, 1, 8, 3, 9, 5, 7, 4, 2],[5, 4, 7, 1, 6, 2, 8, 3, 9],[2, 9, 3, 4, 8, 7, 6, 5, 1],[7, 5, 4, 9, 2, 6, 1, 8, 3],[9, 8, 1, 5, 4, 3, 2, 6, 7],[3, 6, 2, 7, 1, 8, 5, 9, 4]]))\nprint(find_errors([[5, 3, 4, 6, 7, 8, 9, 1, 2],[2, 7, 6, 1, 9, 5, 3, 4, 8],[1, 9, 8, 3, 4, 2, 5, 6, 7],[8, 5, 9, 7, 6, 1, 4, 2, 3],[4, 2, 6, 8, 5, 3, 7, 9, 1],[7, 1, 3, 9, 2, 4, 8, 5, 6],[9, 6, 1, 5, 3, 7, 2, 8, 4],[2, 8, 7, 4, 1, 9, 6, 3, 5],[3, 4, 5, 2, 8, 6, 1, 7, 9]]))\nprint(find_errors([[9, 5, 3, 2, 1, 4, 7, 6, 8],[2, 7, 6, 8, 5, 3, 4, 1, 9],[8, 1, 4, 6, 7, 9, 5, 3, 2],[7, 4, 8, 5, 3, 1, 6, 9, 2],[6, 9, 1, 7, 4, 5, 2, 8, 3],[5, 3, 2, 9, 6, 8, 1, 7, 4],[1, 6, 9, 4, 8, 5, 3, 2, 7],[3, 2, 5, 1, 9, 7, 8, 4, 6],[8, 4, 7, 3, 2, 6, 9, 5, 1]]))\nprint(find_errors([[9, 2, 5, 6, 3, 1, 8, 4, 7],[6, 1, 8, 5, 7, 4, 2, 9, 3],[3, 7, 4, 9, 8, 2, 5, 6, 1],[7, 4, 9, 8, 2, 6, 1, 3, 5],[8, 5, 2, 4, 1, 3, 9, 7, 6],[1, 6, 3, 7, 9, 5, 4, 8, 2],[2, 8, 7, 3, 5, 9, 6, 1, 4],[4, 9, 1, 2, 6, 7, 3, 5, 8],[5, 3, 6, 1, 4 ,8, 7, 2, 9]]))\nprint(find_errors([[1, 2, 5, 6, 3, 1, 8, 4, 7],[6, 1, 8, 5, 7, 4, 2, 9, 3],[3, 7, 4, 9, 8, 2, 5, 6, 1],[7, 4, 9, 8, 2, 6, 1, 3, 5],[8, 5, 2, 4, 1, 3, 9, 7, 6],[1, 6, 3, 7, 9, 5, 4, 8, 2],[2, 8, 7, 3, 5, 9, 6, 1, 4],[4, 9, 1, 2, 6, 7, 3, 5, 8],[5, 3, 6, 1, 4 ,8, 7, 2, 9]]))\nprint(find_errors([[9, 2, 5, 6, 3, 1, 8, 4, 7],[6, 1, 8, 5, 7, 4, 2, 9, 3],[3, 7, 4, 9, 8, 2, 5, 6, 1],[7, 4, 9, 8, 2, 6, 1, 3, 5],[8, 5, 2, 4, 1, 3, 9, 7, 6],[1, 6, 3, 7, 9, 5, 4, 8, 2],[2, 8, 7, 3, 5, 9, 6, 1, 4],[4, 9, 1, 6, 2, 7, 3, 5, 8],[5, 3, 6, 1, 4 ,8, 7, 2, 9]]))\n'
                                        ),
                        /* C++ */       array(
                                            "stub" =>   "int findErrors(int board[][9]) {\n\t\n}",
                                            "tests" =>  'int values[9][9] = {{5, 3, 4, 6, 7, 8, 9, 1, 2},{6, 7, 2, 1, 9, 5, 3, 4, 8},{1, 9, 8, 3, 4, 2, 5, 6, 7},{8, 5, 9, 7, 6, 1, 4, 2, 3},{4, 2, 6, 8, 5, 3, 7, 9, 1},{7, 1, 3, 9, 2, 4, 8, 5, 6},{9, 6, 1, 5, 3, 7, 2, 8, 4},{2, 8, 7, 4, 1, 9, 6, 3, 5},{3, 4, 5, 2, 8, 6, 1, 7, 9}}; cout << findErrors(values) << endl;\nint values1[9][9] = {{1, 7, 5, 8, 3, 9, 4, 2, 6},{6, 3, 6, 2, 7, 4, 9, 1, 5},{4, 2, 9, 6, 5, 1, 3, 7, 8},{8, 1, 8, 3, 9, 5, 7, 4, 2},{5, 4, 7, 1, 6, 2, 8, 3, 9},{2, 9, 3, 4, 8, 7, 6, 5, 1},{7, 5, 4, 9, 2, 6, 1, 8, 3},{9, 8, 1, 5, 4, 3, 2, 6, 7},{3, 6, 2, 7, 1, 8, 5, 9, 4}}; cout << findErrors(values1) << endl;\nint values2[9][9] = {{5, 3, 4, 6, 7, 8, 9, 1, 2},{2, 7, 6, 1, 9, 5, 3, 4, 8},{1, 9, 8, 3, 4, 2, 5, 6, 7},{8, 5, 9, 7, 6, 1, 4, 2, 3},{4, 2, 6, 8, 5, 3, 7, 9, 1},{7, 1, 3, 9, 2, 4, 8, 5, 6},{9, 6, 1, 5, 3, 7, 2, 8, 4},{2, 8, 7, 4, 1, 9, 6, 3, 5},{3, 4, 5, 2, 8, 6, 1, 7, 9}}; cout << findErrors(values2) << endl;\nint values3[9][9] = {{9, 5, 3, 2, 1, 4, 7, 6, 8},{2, 7, 6, 8, 5, 3, 4, 1, 9},{8, 1, 4, 6, 7, 9, 5, 3, 2},{7, 4, 8, 5, 3, 1, 6, 9, 2},{6, 9, 1, 7, 4, 5, 2, 8, 3},{5, 3, 2, 9, 6, 8, 1, 7, 4},{1, 6, 9, 4, 8, 5, 3, 2, 7},{3, 2, 5, 1, 9, 7, 8, 4, 6},{8, 4, 7, 3, 2, 6, 9, 5, 1}}; cout << findErrors(values3) << endl;\nint values4[9][9] = {{9, 2, 5, 6, 3, 1, 8, 4, 7},{6, 1, 8, 5, 7, 4, 2, 9, 3},{3, 7, 4, 9, 8, 2, 5, 6, 1},{7, 4, 9, 8, 2, 6, 1, 3, 5},{8, 5, 2, 4, 1, 3, 9, 7, 6},{1, 6, 3, 7, 9, 5, 4, 8, 2},{2, 8, 7, 3, 5, 9, 6, 1, 4},{4, 9, 1, 2, 6, 7, 3, 5, 8},{5, 3, 6, 1, 4 ,8, 7, 2, 9}}; cout << findErrors(values4) << endl;\nint values5[9][9] = {{1, 2, 5, 6, 3, 1, 8, 4, 7},{6, 1, 8, 5, 7, 4, 2, 9, 3},{3, 7, 4, 9, 8, 2, 5, 6, 1},{7, 4, 9, 8, 2, 6, 1, 3, 5},{8, 5, 2, 4, 1, 3, 9, 7, 6},{1, 6, 3, 7, 9, 5, 4, 8, 2},{2, 8, 7, 3, 5, 9, 6, 1, 4},{4, 9, 1, 2, 6, 7, 3, 5, 8},{5, 3, 6, 1, 4 ,8, 7, 2, 9}}; cout << findErrors(values5) << endl;\nint values6[9][9] = {{9, 2, 5, 6, 3, 1, 8, 4, 7},{6, 1, 8, 5, 7, 4, 2, 9, 3},{3, 7, 4, 9, 8, 2, 5, 6, 1},{7, 4, 9, 8, 2, 6, 1, 3, 5},{8, 5, 2, 4, 1, 3, 9, 7, 6},{1, 6, 3, 7, 9, 5, 4, 8, 2},{2, 8, 7, 3, 5, 9, 6, 1, 4},{4, 9, 1, 6, 2, 7, 3, 5, 8},{5, 3, 6, 1, 4 ,8, 7, 2, 9}}; cout << findErrors(values6) << endl;\n'
                                        )
                    ),
                    "12" => new self(
                        /* id */        12,
                        /* disp_id */   5,
                        /* divisions */ array(2),
                        /* name */      "Smudged Barcode",
                        /* points */    4,
                        /* question */  array(
                                            "Problem" => 'New Wave Computers places a five digit barcode on each of their products that looks like the following:<br><center>|:|::  :|:|:   ||:::   :::||  :|::|</center><br>Each digit in the barcode is comprised of full bars ‘|’ and half bars ‘:’. Each digit is separated by a single space. <br><br>To decode a digit, convert each full bar to a 1 and each half bar to a 0. Then, use the table in the packet to turn the sequences of 0s and 1s into an integer.<br><br>Note that they represent all combinations of two full and three half bars. The digit can be computed easily from the bar code using the column weights 7, 4, 2, 1, 0.  For example, 01100 is<br><br><center>0 * 7 + 1 * 4 + 1 * 2 + 0 * 1 + 0 * 0 = 6</center><br>The only exception is 0, which would yield 11 according to the weight formula.<br><br>Barcodes on several items in New Wave’s warehouse have been smudged making it impossible to read all full bars and half bars.  You are to write a program that reads in smudged barcodes as a String of |’s, :’s, spaces and underscores ‘_’, an underscore represents a smudged full bar or half bar, and returns a collection of all possible barcodes that the smudged barcode could represent.  For example if your method was passed the String <br><center>|:|::  :|:|:   ||:::   :::||  :|:_|</center><br>The first five digits in this barcode would evaluate to 9501.  The missing character in the last digit would have to be a half bar since each digit must consist of three half bars and two full bars, which would make the only possible barcode for this smudged String 95014.  If however your method was passed the string<br><center>|:|::  :|:|:   ||:::   :::||  :_:_|</center><br>the final digit could be either a 1 or a 4 so there would be two possible barcodes that you could generate from this String, 95011 or 95014.  There will not be more than three underscores in a single digit of the barcode.',
                                            "Input" => 'String barCode - A String of |s, :s, spaces, and underscores. An underscore represents a smudged full bar or half bar.',
                                            "Output" => 'Java: An ArrayList of Strings representing all combinations that could be generated from the input barCode.<br>Python: A list of Strings representing all combinations that could be generated from the input barCode.<br>C++: An array of Strings representing all combinations that could be generated from the input barCode.',
                                        ),
                        /* sample */    array(
                                            '|:|_: ||_:: |_:_| ||_:_ :|__|' => '<code>{90704}</code>',
                                            '|___: ||__: |___: ::___ :|::|' => '<code>{00014, 00024, 00034, 00814, 00824, 00834, 00914, 00924, 00934, 80014, 80024, 80034, 80814, 80824, 80834, 80914, 80924, 80934, 90014, 90024, 90034, 90814, 90824, 90834, 90914, 90924, 90934}</code>',
                                            '|_:_: ||_:: |___: ::_|_ :|_:|' => '<code>{00014, 00034, 00814, 00834, 00914, 00934, 80014, 80034, 80814, 80834, 80914, 80934}</code>',
                                            '|_|_: |__:: |___| |__:_ :___|' => '<code>{90701, 90702, 90704, 90771, 90772, 90774, 90791, 90792, 90794, 99701, 99702, 99704, 99771, 99772, 99774, 99791, 99792, 99794}</code>'
                                        ),
                        /* correct */   array("00014", "00014", "00024", "00034", "00034", "00814", "00814", "00824", "00834", "00834", "00914", "00914", "00924", "00934", "00934", "30771", "30772", "30774", "30781", "30782", "30784", "30791", "30792", "30794", "37771", "37772", "37774", "37781", "37782", "37784", "37791", "37792", "37794", "38771", "38772", "38774", "38781", "38782", "38784", "38791", "38792", "38794", "80014", "80014", "80024", "80034", "80034", "80771", "80772", "80774", "80781", "80782", "80784", "80791", "80792", "80794", "80814", "80814", "80824", "80834", "80834", "80914", "80914", "80924", "80934", "80934", "87771", "87772", "87774", "87781", "87782", "87784", "87791", "87792", "87794", "88771", "88772", "88774", "88781", "88782", "88784", "88791", "88792", "88794", "90014", "90024", "90034", "90142", "90152", "90162", "90242", "90252", "90262", "90442", "90452", "90462", "90701", "90702", "90704", "90771", "90771", "90772", "90772", "90774", "90774", "90781", "90782", "90784", "90791", "90791", "90792", "90792", "90794", "90794", "90814", "90824", "90834", "90914", "90924", "90934", "97771", "97772", "97774", "97781", "97782", "97784", "97791", "97792", "97794", "98771", "98772", "98774", "98781", "98782", "98784", "98791", "98792", "98794", "99701", "99702", "99704", "99771", "99772", "99774", "99791", "99792", "99794", "90704"),
                        /* java */      array(
                                            "stub" =>   "public static ArrayList<String> getPossibleBarcodes(String barcode) {\n\t\n}",
                                            "tests" =>  'for (String a: getPossibleBarcodes("|:|_: ||_:: |_:_| ||_:_ :|__|")) { System.out.println(a); }\nfor (String a: getPossibleBarcodes("|___: ||__: |___: ::___ :|::|")) { System.out.println(a); }\nfor (String a: getPossibleBarcodes("|_:_: ||_:: |___: ::_|_ :|_:|")) { System.out.println(a); }\nfor (String a: getPossibleBarcodes("|_|_: |__:: |___| |__:_ :___|")) { System.out.println(a); }\nfor (String a: getPossibleBarcodes("_:__: |_:__ |___| |:___ :___|")) { System.out.println(a); }\nfor (String a: getPossibleBarcodes("|:|_: ||:__ :___| :|___ :_|_|")) { System.out.println(a); }\n'
                                        ),
                        /* python */    array(
                                            "stub" =>   "def get_possible_barcodes(barcode):\n\t",
                                            "tests" =>  'for (a in get_possible_barcodes("|:|_: ||_:: |_:_| ||_:_ :|__|")): print a\nfor (a in get_possible_barcodes("|___: ||__: |___: ::___ :|::|")): print a\nfor (a in get_possible_barcodes("|_:_: ||_:: |___: ::_|_ :|_:|")): print a\nfor (a in get_possible_barcodes("|_|_: |__:: |___| |__:_ :___|")): print a\nfor (a in get_possible_barcodes("_:__: |_:__ |___| |:___ :___|")): print a\nfor (a in get_possible_barcodes("|:|_: ||:__ :___| :|___ :_|_|")): print a\n'
                                        ),
                        /* C++ */       null,
                        /* hand_gr */   false,
                        /* case_sens */ false,
                        /* sort */      true
                    ),
                    "13" => new self(
                        /* id */        13,
                        /* disp_id */   6,
                        /* divisions */ array(2),
                        /* name */      "Special Summations: The Unsolvable Problem",
                        /* points */    4,
                        /* question */  array(
                                            "Problem" => 'The New Wave hiring department has designed a new question for when they interview programmers. However, they can’t solve the problem. Given a positive integer, n, they would like to know all unique combinations of positive numbers that would add up to n.  A summation cannot contain more than one of each digit, that is, you cannot have a summation that uses two 1s or two 2s, etc.',
                                            "Input" => 'An integer, <code>n</code>, with a maximum value of 20.',
                                            "Output" => 'All summations of n. Each summation is a sequence of numbers that, when added together, result in n. A summation cannot contain more than one of each digit, that is, you cannot have a summation that uses two 1s or two 2s, etc.  Also, the numbers in the sequence must be arranged from smallest to largest. Each summation should be represented as a string with the numbers separated by spaces and addition signs.<br><br>Java - An ArrayList of Strings representing all unique summations for n.<br>Python - A List of Strings representing all unique summations for n.<br>C++ -  An array of Strings representing all unique summations for n.',
                                            "Note" => 'Your output should look exactly like the samples with no extraneous spaces or addition signs. For this problem only, the runtime can be greater than 10 seconds. If your solution times out, tell a judge.'
                                        ),
                        /* sample */    array(
                                            '<code>5</code>' => '<code>{"1 + 4", "2 + 3"}</code>',
                                            '<code>8</code>' => '<code>{"1 + 7", "2 + 6", "3 + 5", "1 + 3 + 4", "1 + 2 + 5"}</code>',
                                            '<code>12</code>' => '<code>{"1 + 11", "1 + 2 + 9", "1 + 2 + 3 + 6", "1 + 2 + 4 + 5", "1 + 3 + 8", "1 + 4 + 7", "1 + 5 + 6", "2 + 10", "2 + 3 + 7", "2 + 4 + 6", "3 + 9", "3 + 4 + 5", "4 + 8", "5 + 7"}</code>',
                                        ),
                        /* correct */   array("1 + 4", "2 + 3", "1 + 7", "1 + 2 + 5", "1 + 3 + 4", "2 + 6", "3 + 5", "1 + 11", "1 + 2 + 9", "1 + 2 + 3 + 6", "1 + 2 + 4 + 5", "1 + 3 + 8", "1 + 4 + 7", "1 + 5 + 6", "2 + 10", "2 + 3 + 7", "2 + 4 + 6", "3 + 9", "3 + 4 + 5", "4 + 8", "5 + 7", "1 + 10", "1 + 2 + 8", "1 + 2 + 3 + 5", "1 + 3 + 7", "1 + 4 + 6", "2 + 9", "2 + 3 + 6", "2 + 4 + 5", "3 + 8", "4 + 7", "5 + 6", "1 + 19", "1 + 2 + 17", "1 + 2 + 3 + 14", "1 + 2 + 3 + 4 + 10", "1 + 2 + 3 + 5 + 9", "1 + 2 + 3 + 6 + 8", "1 + 2 + 4 + 13", "1 + 2 + 4 + 5 + 8", "1 + 2 + 4 + 6 + 7", "1 + 2 + 5 + 12", "1 + 2 + 6 + 11", "1 + 2 + 7 + 10", "1 + 2 + 8 + 9", "1 + 3 + 16", "1 + 3 + 4 + 12", "1 + 3 + 4 + 5 + 7", "1 + 3 + 5 + 11", "1 + 3 + 6 + 10", "1 + 3 + 7 + 9", "1 + 4 + 15", "1 + 4 + 5 + 10", "1 + 4 + 6 + 9", "1 + 4 + 7 + 8", "1 + 5 + 14", "1 + 5 + 6 + 8", "1 + 6 + 13", "1 + 7 + 12", "1 + 8 + 11", "1 + 9 + 10", "2 + 18", "2 + 3 + 15", "2 + 3 + 4 + 11", "2 + 3 + 4 + 5 + 6", "2 + 3 + 5 + 10", "2 + 3 + 6 + 9", "2 + 3 + 7 + 8", "2 + 4 + 14", "2 + 4 + 5 + 9", "2 + 4 + 6 + 8", "2 + 5 + 13", "2 + 5 + 6 + 7", "2 + 6 + 12", "2 + 7 + 11", "2 + 8 + 10", "3 + 17", "3 + 4 + 13", "3 + 4 + 5 + 8", "3 + 4 + 6 + 7", "3 + 5 + 12", "3 + 6 + 11", "3 + 7 + 10", "3 + 8 + 9", "4 + 16", "4 + 5 + 11", "4 + 6 + 10", "4 + 7 + 9", "5 + 15", "5 + 6 + 9", "5 + 7 + 8", "6 + 14", "7 + 13", "8 + 12", "9 + 11"),
                        /* java */      array(
                                            "stub" =>   "public static List<String> getSummations(int n) {\n\t\n}",
                                            "tests" =>  'for (String a: getSummations(5)) { System.out.println(a); }\nfor (String a: getSummations(8)) { System.out.println(a); }\nfor (String a: getSummations(12)) { System.out.println(a); }\nfor (String a: getSummations(11)) { System.out.println(a); }\nfor (String a: getSummations(20)) { System.out.println(a); }\n'
                                        ),
                        /* python */    array(
                                            "stub" =>   "def get_summations(n):\n\t",
                                            "tests" =>  'for (a in get_summations(5)): print a\nfor (a in get_summations(8)): print a\nfor (a in get_summations(12)): print a\nfor (a in get_summations(11)): print a\nfor (a in get_summations(20)): print a\n'
                                        ),
                        /* C++ */       null,
                        /* hand_gr */   false,
                        /* case_sens */ false,
                        /* sort */      true
                    ),
                    "14" => new self(
                        /* id */        14,
                        /* disp_id */   7,
                        /* divisions */ array(2),
                        /* name */      "Diecisiete",
                        /* points */    "up to 8",
                        /* question */  array(
                                            "Problem" => 'New Wave Computers’ Gaming Division’s newest release will be the game of Diecisiete.  This game is played using a modified Uno deck.  The object of the game of Diecisiete is to beat the computer in one of the following ways: reach a final score higher than the computer without exceeding 17 or be dealt 5 cards without exceeding a value of 17.',
                                            "Rules" => 'At the start of the game the player and computer are each dealt two cards.  The computer and player take turns throughout the game either drawing another card or staying.  Once the computer or player has decided to ‘stay’ they can no longer ‘draw’ additional cards. Play will continue until the the other contestant either ‘stays’, is dealt five cards or exceeds a sum of 17.  The player will be given the option to either stay or draw throughout the game.  The computer should continue to draw until it’s hand has a total greater than 13 or it can see that the player has exceeded 17.  The computer cannot ‘see’ the first card in the player’s hand so the summation that the computer uses to to determine if it should draw or stay should not take this first card into account.<br><br>The modified Uno deck contains 80 cards. There are four suits: red, green, yellow and blue.  Each color consists of one 0 card, two 1s, two 2s, two 3s, two 4s, two 5s, two 6s, two 7s, two 8s and two 9s. The player\'s and dealer’s score is calculated by summing the values of each card in their hands.  When calculating this sum each rank card has a value equal to its rank.<br><br>During game play you cannot see the dealer’s first card but will be able to see any card after this.  Cards should be randomly dealt.  You do not need to keep track of which cards have already been dealt, but should instead generate a random number between 0 and 3 for the card’s color (0 – red, 1 - green, 2 - blue, 3 – yellow) and a random number between 0 and 9 for it’s value.  Zero through nine represent a card with ranks from zero to nine.',
                                            "Note" => 'This problem will be graded by a real-life developer. The rubric by which it will be graded is at the back of this packet. Note that a non-working or partially-working project can still receive points for design and organization.',
                                        ),
                        /* sample */    'Welcome to Diecisiete.<br><br>Computer’s hand: * R4<br><br>Your hand: Y5 G3<br><br>The computer has chosen to draw another card.<br><br>Computer’s hand: * R4 Y2<br><br>Your hand: Y5 G3<br><br>Enter a 1 to draw or a 2 to stay<br><br>1<br><br>Computer’s hand: * R4 Y2<br><br>Your hand: Y5 G3 G7<br><br>The computer has chosen to stay.<br><br>Computer’s hand: * R4 Y2<br><br>Your hand: Y5 G3 G7<br><br>Enter a 1 to draw or a 2 to stay<br><br>2<br><br>Computer’s hand: B9 R4 Y2<br><br>Your hand: Y5 G3 G7<br><br>The computer’s total is 15.  Your total is 15.  The computer wins.',
                        /* correct */   null,
                        /* java */      null,
                        /* python */    null,
                        /* C++ */       null,
                        /* hand_gr */   true
                    ),
                    "16" => new self(
                        /* id */        16,
                        /* disp_id */   1,
                        /* divisions */ array(3),
                        /* name */      "Uncrackable Encryption",
                        /* points */    2,
                        /* question */  array(
                                            "Problem" => 'You are writing an uncrackable encryption algorithm.',
                                            "Input" => 'A lowercase string of six letters, <code>input</code>.',
                                            "Output" => 'Perform the following steps in order and return a new string:<br><br><ol><li>Increment each letter by 1.</li><li>Reverse the string.</li><li>Capitalize every other letter.</li></ol><hr>These methods might be useful if you are using Java:<ul><li>String.charAt(int): returns the character at the given index.</li><li>Character.toUpperCase(char): returns the uppercase version of a character.</li></ul>'
                                        ),
                        /* sample */    array('coding' => 'HoJePd', 'python' => 'OpIuZq', 'string' => 'HoJsUt'),
                        /* correct */   array("HoJePd", "OpIuZq", "HoJsUt"),
                        /* java */      array(
                                            "stub" =>   "public static String encrypt(String input) {\n\t\n}",
                                            "tests" =>  'System.out.println(encrypt("coding"));\nSystem.out.println(encrypt("python"));\nSystem.out.println(encrypt("string"));\n'
                                        ),
                        /* python */    array(
                                            "stub" =>   "def encrypt(input):\n\t",
                                            "tests" =>  'print(encrypt("coding"))\nprint(encrypt("python"))\nprint(encrypt("string"))\n'
                                        ),
                        /* C++ */       array(
                                            "stub" =>   "string encrypt(string input) {\n\t\n}",
                                            "tests" =>  'cout << encrypt("coding") << endl;\ncout << encrypt("python") << endl;\ncout << encrypt("string") << endl;\n'
                                        )
                    ),
                    "17" => new self(
                        /* id */        17,
                        /* disp_id */   2,
                        /* divisions */ array(3),
                        /* name */      "Pretty Rectangle",
                        /* points */    2,
                        /* question */  array(
                                            "Problem" => 'You are writing a program to print a pretty rectangle.',
                                            "Input" => 'An integer <code>n</code> from 1 to 25.',
                                            "Output" => 'An nxn rectangle with a border of dashes (-) and an interior of exclamation points (!). There should be no whitespace, and each line should be followed by exactly one newline. Do not hardcode your answer; use loops. Print out each line and don\'t return anything.'
                                        ),
                        /* sample */    array('<code>1</code>' => '-', '<code>2</code>' => '--<br>--', '<code>3</code>' => '---<br>-!-<br>---', '<code>5</code>' => '-----<br>-!!!-<br>-!!!-<br>-!!!-<br>-----'),
                        /* correct */   array("-", /**/ "--", "--", /**/ "---", "-!-", "---", /**/ "-----", "-!!!-", "-!!!-", "-!!!-", "-----", /**/ "--------", "-!!!!!!-", "-!!!!!!-", "-!!!!!!-", "-!!!!!!-", "-!!!!!!-", "-!!!!!!-", "--------", /**/ "----------------", "-!!!!!!!!!!!!!!-", "-!!!!!!!!!!!!!!-", "-!!!!!!!!!!!!!!-", "-!!!!!!!!!!!!!!-", "-!!!!!!!!!!!!!!-", "-!!!!!!!!!!!!!!-", "-!!!!!!!!!!!!!!-", "-!!!!!!!!!!!!!!-", "-!!!!!!!!!!!!!!-", "-!!!!!!!!!!!!!!-", "-!!!!!!!!!!!!!!-", "-!!!!!!!!!!!!!!-", "-!!!!!!!!!!!!!!-", "-!!!!!!!!!!!!!!-", "----------------"),
                        /* java */      array(
                                            "stub" =>   "public static void shape(int n) {\n\t\n}",
                                            "tests" =>  'shape(1);\nshape(2);\nshape(3);\nshape(5);\nshape(8);\nshape(16);\n'
                                        ),
                        /* python */    array(
                                            "stub" =>   "def shape(n):\n\t",
                                            "tests" =>  'shape(1)\nshape(2)\nshape(3)\nshape(5)\nshape(8)\nshape(16)\n'
                                        ),
                        /* C++ */       array(
                                            "stub" =>   "string shape(int n) {\n\t\n}",
                                            "tests" =>  'shape(1);\nshape(2);\nshape(3);\nshape(5);\nshape(8);\nshape(16);\n'
                                        )
                    ),
                    "18" => new self(
                        /* id */        18,
                        /* disp_id */   3,
                        /* divisions */ array(3),
                        /* name */      "Number Checker",
                        /* points */    2,
                        /* question */  array(
                                            "Problem" => 'You are writing a number-checking program.',
                                            "Input" => 'An integer value <code>n</code> of six digits.',
                                            "Output" => 'Determine whether or not no more than 2 digits are greater than the average of all of the digits. Return <code>true</code> if the condition is met and <code>false</code> otherwise.'
                                        ),
                        /* sample */    array('<code>111999</code>' => '<code>false</code>', '<code>555555</code>' => '<code>true</code>', '<code>123452</code>' => '<code>false</code>'),
                        /* correct */   array("false", "false", "true", "false", "false", "true"),
                        /* java */      array(
                                            "stub" =>   "public static boolean checkNumber(int n) {\n\t\n}",
                                            "tests" =>  'System.out.println(checkNumber(111999));\nSystem.out.println(checkNumber(123452));\nSystem.out.println(checkNumber(555555));\nSystem.out.println(checkNumber(123456));\nSystem.out.println(checkNumber(696969));\nSystem.out.println(checkNumber(420420));\n'
                        ),
                        /* python */    array(
                                            "stub" =>   "def check_number(n):\n\t",
                                            "tests" =>  'print(check_number(111999))\nprint(check_number(123452))\nprint(check_number(555555))\nprint(check_number(123456))\nprint(check_number(696969))\nprint(check_number(420420))\n'
                        ),
                        /* C++ */       array(
                                            "stub" =>   "bool checkNumber(int n) {\n\t\n}",
                                            "tests" =>  'cout << checkNumber(111999) << endl;\ncout << checkNumber(123452) << endl;\ncout << checkNumber(555555) << endl;\ncout << checkNumber(123456) << endl;\ncout << checkNumber(696969) << endl;\ncout << checkNumber(420420) << endl;\n'
                        )
                    ),
                    "19" => new self(
                        /* id */        19,
                        /* disp_id */   1,
                        /* divisions */ array(4),
                        /* name */      "Goldbach Conjecture",
                        /* points */    2,
                        /* question */  array(
                                            "Problem" => 'The Goldbach Conjecture states that any even number greater than two can be expressed as a sum of two prime numbers.',
                                            "Input" => 'An integer value <code>n</code> which is even and greater than 2.',
                                            "Output" => 'Determine any one pair of prime numbers that add to <code>n</code>. The result should be a string formatted as "a + b" with exactly the right amount of whitespace where a and b are the two numbers.'
                                        ),
                        /* sample */    array('<code>8</code>' => '3 + 5', '<code>4</code>' => '2 + 2', '<code>10</code>' => '5 + 5'),
                        /* correct */   array(array("3 + 5", "5 + 3"), "2 + 2", array("5 + 5", "3 + 7", "7 + 3"), array("5 + 7", "7 + 5"), "3 + 3"),
                        /* java */      array(
                                            "stub" =>   "public static String goldbach(int n) {\n\t\n}",
                                            "tests" =>  'System.out.println(goldbach(8));\nSystem.out.println(goldbach(4));\nSystem.out.println(goldbach(10));\nSystem.out.println(goldbach(12));\nSystem.out.println(goldbach(6));\n'
                        ),
                        /* python */    array(
                                            "stub" =>   "def goldbach(n):\n\t",
                                            "tests" =>  'print(goldbach(8))\nprint(goldbach(4))\nprint(goldbach(10))\nprint(goldbach(12))\nprint(goldbach(6))\n'
                        ),
                        /* C++ */       array(
                                            "stub" =>   "string goldbach(int n) {\n\t\n}",
                                            "tests" =>  'cout << goldbach(8) << endl;\ncout << goldbach(4) << endl;\ncout << goldbach(10) << endl;\ncout << goldbach(12) << endl;\ncout << goldbach(6) << endl;\n'
                        )
                    ),
                    "20" => new self(
                        /* id */        20,
                        /* disp_id */   2,
                        /* divisions */ array(4),
                        /* name */      "Uncrackable Encryption",
                        /* points */    2,
                        /* question */  array(
                                            "Problem" => 'You are writing an uncrackable encryption algorithm.',
                                            "Input" => 'A lowercase string of variable length, <code>input</code>.',
                                            "Output" => 'Perform the following steps in order and return a new string:<br><br><ol><li>For each word in the string (words are separated by spaces):</li><ol type=\"a\"><li>Increment each letter by 1.</li><li>Reverse the string.</li><li>Capitalize every other letter.</li></ol><li>Concatenate all of the encrypted words.</li></ol><hr>These methods might be useful if you are using Java:<ul><li>String.charAt(int): returns the character at the given index.</li><li>Character.toUpperCase(char): returns the uppercase version of a character.</li></ul>'
                                        ),
                        /* sample */    array('coding' => 'HoJePd', 'python' => 'OpIuZq', 'string' => 'HoJsUt', 'i like pie' => 'J FlJm FjQ'),
                        /* correct */   array("HoJePd", "OpIuZq", "HoJsUt", "J FlJm FjQ", "BwBk YpS", "YbN"),
                        /* java */      array(
                                            "stub" =>   "public static String encrypt(String input) {\n\t\n}",
                                            "tests" =>  'System.out.println(encrypt("coding"));\nSystem.out.println(encrypt("python"));\nSystem.out.println(encrypt("string"));\nSystem.out.println(encrypt("i like pie"));\nSystem.out.println(encrypt("java rox"));\nSystem.out.println(encrypt("max"));\n'
                        ),
                        /* python */    array(
                                            "stub" =>   "def encrypt(input):\n\t",
                                            "tests" =>  'print(encrypt("coding"))\nprint(encrypt("python"))\nprint(encrypt("string"))\nprint(encrypt("i like pie"))\nprint(encrypt("java rox"))\nprint(encrypt("max"))\n'
                        ),
                        /* C++ */       array(
                                            "stub" =>   "string encrypt(string input) {\n\t\n}",
                                            "tests" =>  'cout << encrypt("coding") << endl;\ncout << encrypt("python") << endl;\ncout << encrypt("string") << endl;\ncout << encrypt("i like pie") << endl;\ncout << encrypt("java rox") << endl;\ncout << encrypt("max") << endl;\n'
                        )
                    ),
                    "21" => new self(
                        /* id */        21,
                        /* disp_id */   3,
                        /* divisions */ array(4),
                        /* name */      "Anagram Checker",
                        /* points */    2,
                        /* question */  array(
                                            "Problem" => 'You are writing a program to determine whether two words are anagrams of each other. Two words are anagrams of each other if they contain the same number of each character.',
                                            "Input" => 'Two strings, <code>a</code> and <code>b</code>, containing lowercase letters a-z and possibly spaces.',
                                            "Output" => 'Determine whether or not they are anagrams of each other. Return <code>true</code> if they are and <code>false</code> if they are not.'
                                        ),
                        /* sample */    array('cinema, iceman' => '<code>true</code>', 'one, two' => '<code>false</code>', 'dormitory, dirty room' => '<code>true</code>'),
                        /* correct */   array("true", "false", "true", "true", "false", "true"),
                        /* java */      array(
                                            "stub" =>   "public static boolean areAnagrams(String a, String b) {\n\t\n}",
                                            "tests" =>  'System.out.println(areAnagrams("cinema", "iceman"));\nSystem.out.println(areAnagrams("one", "two"));\nSystem.out.println(areAnagrams("dormitory", "dirty room"));\nSystem.out.println(areAnagrams("the eyes", "they see"));\nSystem.out.println(areAnagrams("max", "noah"));\nSystem.out.println(areAnagrams("election results", "lies lets recount"));\n'
                        ),
                        /* python */    array(
                                            "stub" =>   "def are_anagrams(a, b):\n\t",
                                            "tests" =>  'print(are_anagrams("cinema", "iceman"))\nprint(are_anagrams("one", "two"))\nprint(are_anagrams("dormitory", "dirty room"))\nprint(are_anagrams("the eyes", "they see"))\nprint(are_anagrams("max", "noah"))\nprint(are_anagrams("election results", "lies lets recount"))\n'
                        ),
                        /* C++ */       array(
                                            "stub" =>   "bool areAnagrams(string a, string b) {\n\t\n}",
                                            "tests" =>  'cout << areAnagrams("cinema", "iceman") << endl;\ncout << areAnagrams("one", "two") << endl;\ncout << areAnagrams("dormitory", "dirty room") << endl;\ncout << areAnagrams("the eyes", "they see") << endl;\ncout << areAnagrams("max", "noah") << endl;\ncout << areAnagrams("election results", "lies lets recount") << endl;\n'
                        )
                    ),
                    "97" => new self(
                        /* id */        97,
                        /* disp_id */   1,
                        /* divisions */ array(0),
                        /* name */      "Even or odd?",
                        /* points */    2,
                        /* question */  array(
                                            "Problem" => 'You are writing a program to determine whether a number is even or odd.',
                                            "Input" => 'An integer, <code>n</code>.',
                                            "Output" => '<code>true</code> if the number is even and <code>false</code> if it is odd. Assume that 0 is even.'
                                        ),
                        /* sample */    array("0" => "<code>true</code>", "1" => "<code>false</code>", "2" => "<code>true</code>"),
                        /* correct */   array("true", "false", "true", "false", "true"),
                        /* java */      array(
                                            "stub" =>   "public static boolean isEven(int n) {\n\t\n}",
                                            "tests" =>  'System.out.println(isEven(0)); System.out.println(isEven(1)); System.out.println(isEven(2)); System.out.println(isEven(999)); System.out.println(isEven(1000));'
                                        ),
                        /* python */    array(
                                            "stub" =>   "def is_even(n):\n\t",
                                            "tests" =>  'print(is_even(0))\nprint(is_even(1))\nprint(is_even(2))\nprint(is_even(999))\nprint(is_even(1000))\n'
                                        ),
                        /* C++ */       array(
                                            "stub" =>   "bool isEven(int n) {\n\t\n}",
                                            "tests" =>  'cout << isEven(0) << endl;cout << isEven(1) << endl;cout << isEven(2) << endl;cout << isEven(999) << endl;cout << isEven(1000) << endl;'
                                        )
                    ),
                    "98" => new self(
                        /* id */        98,
                        /* disp_id */   2,
                        /* divisions */ array(0),
                        /* name */      "How do you do?",
                        /* points */    4,
                        /* question */  array(
                                            "Problem" => 'You are writing a program to create a nice greeting given a name.',
                                            "Input" => 'A string, <code>name</code>.',
                                            "Output" => 'A nice greeting in the format <i>Hello, {name}!</i>'
                                        ),
                        /* sample */    array("Noah" => "Hello, Noah!", "Dog" => "Hello, Dog!"),
                        /* correct */   array("Hello, Noah!", "Hello, Dog!", "Hello, 1234!", "Hello, test!"),
                        /* java */      array(
                                            "stub" =>   "public static String greet(String name) {\n\t\n}",
                                            "tests" =>  'System.out.println(greet("Noah")); System.out.println(greet("Dog")); System.out.println(greet("1234")); System.out.println(greet("test"));'
                                        ),
                        /* python */    array(
                                            "stub" =>   "def greet(name):\n\t",
                                            "tests" =>  'print(greet("Noah"))\nprint(greet("Dog"))\nprint(greet("1234"))\nprint(greet("test"))\n'
                                        ),
                        /* C++ */       array(
                                            "stub" =>   "string greet(string name) {\n\t\n}",
                                            "tests" =>  'cout << greet("Noah") << endl;cout << greet("Dog") << endl;cout << greet("1234") << endl;cout << greet("test") << endl;'
                                        )
                    ),
                    "99" => new self(
                        /* id */        99,
                        /* disp_id */   3,
                        /* divisions */ array(0),
                        /* name */      "High-Low",
                        /* points */    "up to 8",
                        /* question */  array(
                                            "Problem" => 'You are writing an implementation of the classic game high-low.',
                                            "Rules" => 'You will generate a random number between 1 and 100 inclusive. You will then ask the user for their guess and tell them if it is too high or too low until they get the correct answer. When they do, you should tell them how many guesses it took them to get the answer.',
                                            "Note" => 'This problem will be graded by a real-life developer. The rubric by which it will be graded is at the back of this packet. Note that a non-working or partially-working project can still receive points for design and organization.',
                                        ),
                        /* sample */    'Enter a guess.<br>50<br>Too high.<br>Enter a guess.<br>25<br>Too high.<br>Enter a guess.<br>15<br>You got it in 3 guesses!',
                        /* correct */   null,
                        /* java */      null,
                        /* python */    null,
                        /* C++ */       null,
                        /* hand_graded */ true
                    )
                );
            }
        }

        static function all($team) {
            self::setup();

            $problems = array();

            foreach (self::$all as $id => $problem) {
                if (in_array($team->division, $problem->divisions)) {
                    array_push($problems, $problem);
                }
            }

            return $problems;
        }
    }

    class Submission {
        var $id, $problem, $team, $languageID, $language, $language_extension, $name, $date, $result;

        function __construct($id) {
            $mysql = get_mysql();
            $data = $mysql->query("select * from submissions where id = " . $mysql->escape_string($id))->fetch_assoc();
            $this->id = $data["id"];
            $this->problem = $data["problem"];
            $this->team = $data["team"];
            $this->languageID = $data["language"];
            $this->language = $this->languageID == -1 ? "Manual" : ($this->languageID == 0 ? "Java" : ($this->languageID == 1 ? "Python" : "C++"));
            $this->language_extension = "." . ($this->languageID == -1 ? "txt" : ($this->languageID == 0 ? "java" : ($this->languageID == 1 ? "py" : "cpp")));
            $this->name = $data["name"];
            $this->date = $data["date"];
            $this->result = $data["result"];
        }

        public static function create($problem, $team, $language, $name, $result) {
            $mysql = get_mysql();
            $mysql->query("insert into submissions (problem, team, language, name, date, result) values (" . $mysql->escape_string($problem) . ", " . $mysql->escape_string($team) . ", " . $mysql->escape_string($language) . ", '" . $mysql->escape_string($name) . "', '" . date("Y-m-d H:i:s") . "', '" . $mysql->escape_string($result) . "')");
            return $mysql->insert_id;
        }

        public static function for_team($team) {
            $submissions = array();

            $mysql = get_mysql();
            foreach ($mysql->query("select id from submissions where team = " . $mysql->escape_string($team)) as $data) {
                array_push($submissions, new self($data["id"]));
            }

            return array_reverse($submissions);
        }
        
        public static function for_team_problem($team, $problem) {
            $submissions = array();
            
            foreach (self::for_team($team) as $submission) {
                if ($submission->problem == $problem) {
                    array_push($submissions, $submission);
                }
            }
            
            return $submissions;
        }
    }
    
    function get_mysql() {
        $file = "codelm.txt";

        while (!file_exists($file)) {
            $file = "../" . $file;
        }

        $conn = new mysqli("localhost", "root", trim(file_get_contents($file)), "codelm");

        if ($conn->connect_error) {
            die("Connection error: " . $conn->connect_error);
        }

        return $conn;
    }
?>