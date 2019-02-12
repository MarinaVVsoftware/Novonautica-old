<?php
class _KMail	{

    var $_host		=	FALSE;
    var $_username		=	FALSE;
    var $_password		=	FALSE;
    var $_port		=	FALSE;
    var $_secure		=	FALSE;

    # number of sended mails in one session

    var $_sessionLimit	=	50;

    # mail

    var $_from		=	'';
    var $_to		=	array();
    var $_cc		=	array();
    var $_bcc		=	array();
    var $_recipientsList	=	array();
    var $_primaryTo		=	'';
    var $_subject		=	'';
    var $_message		=	'';
    var $_messageFile	=	'';
    var $_text		=	FALSE;
    var $_files		=	array();
    var $_sender		=	'';

    var $_mailMessage	=	'';
    var $_notificationTo	=	FALSE;
    var $_mailListMode	=	FALSE;
    var $_unsubscribeMail	=	FALSE;
    var $_unsubscribeUrl	=	FALSE;
    var $_listIdStr		=	FALSE;
    var $_embedd		=	FALSE;
    var $_inlines		=	FALSE;

    # debug

    var $_debug		=	FALSE;

    # report & error & auth

    var $_reportStr		=	'';
    var $_serverAuth	=	FALSE;
    var $_timeLimit		=	0;
    var $_rcpDelay		=	0;
    var $_conDelay		=	1;

    var $_EOL		=	"\r\n";
    var $_WSP		=	' ';

    var $_STARTTLS 		=	FALSE;

    /**
     * _KMail::__construct()
     *
     * @return
     */
    function __construct()		{

        $this->INTERNAL_ENC = mb_internal_encoding();

        mb_internal_encoding('UTF-8');

        $this->REGEX_ENC = mb_regex_encoding();

        mb_regex_encoding('UTF-8');
    }

    /**
     * _KMail::host()
     *
     * @param mixed $host
     * @return
     */
    public function host($host)	{

        $this->_host = $host;
    }

    /**
     * _KMail::user()
     *
     * @param mixed $user
     * @return
     */
    public function user($user)	{

        $this->_username = $user;
    }

    /**
     * _KMail::password()
     *
     * @param mixed $pass
     * @return
     */
    public function password($pass)	{

        $this->_password = $pass;
    }

    /**
     * _KMail::port()
     *
     * @param mixed $port
     * @return
     */
    public function	port($port)	{

        if ((integer)$port <= 0) return;

        $this->_port=(integer)$port;
    }

    /**
     * _KMail::secure()
     *
     * @param mixed $type
     * @return
     */
    public function secure($type)	{

        if (strpos(strtolower($type), 'ssl') !== FALSE)	{

            $this->_secure = 'ssl://';

        }	elseif (strpos(strtolower($type), 'tls') !== FALSE)	{

            $this->_secure = 'tls://';

        }	else	{

            $this->addToReport ("Invalid secure connection type defined: $type.");
        }
    }


    # number of sended mails in one session

    /**
     * _KMail::limit()
     *
     * @param mixed $int
     * @return
     */
    public function	limit($int)	{

        if (! (integer)$int) return;

        $this->_sessionLimit	=	(integer)$int;
    }

    # display the command & server responses

    /**
     * _KMail::debug()
     *
     * @return
     */
    public function debug()	{

        $this->_debug		=	TRUE;
    }

    # senders mail address

    /**
     * _KMail::from()
     *
     * @param mixed $mail
     * @return
     */
    public function from($mail)	{

        $this->_from = $this->stripInvalidChars($mail);
    }

    # reply-to mail address

    /**
     * _KMail::reply()
     *
     * @param bool $mail
     * @return
     */
    public function reply($mail=FALSE)	{

        $this->_replyTo = $this->stripInvalidChars($mail);
    }

    # recipients addresses

    /**
     * _KMail::to()
     *
     * @param mixed $mails
     * @return
     */
    public function to($mails)	{

        if (! isset($mails))	return;

        if (! is_array($mails))	{

            if ($this->isEmpty($mails)) return;

            $mails = $this->commaSepExplode($mails);
        }

        $this->_primaryTo = $mails[0];

        $this->_to = $mails;
    }

    # carbon copy recipients addresses

    /**
     * _KMail::cc()
     *
     * @param mixed $mails
     * @return
     */
    public function cc($mails)	{

        if (! isset($mails))	return;

        if (! is_array($mails))	{

            if ($this->isEmpty($mails)) return;

            $mails = $this->commaSepExplode($mails);
        }

        $this->_cc = $mails;
    }

    # blind carbon copy recipients addresses

    /**
     * _KMail::bcc()
     *
     * @param mixed $mails
     * @return
     */
    public function bcc($mails)	{

        if (! isset($mails))	return;

        if (! is_array($mails))	{

            if ($this->isEmpty($mails)) return;

            $mails = $this->commaSepExplode($mails);
        }

        $this->_bcc = $mails;
    }

    # notification

    /**
     * _KMail::notify()
     *
     * @param bool $mail
     * @return
     */
    public function notify($mail = FALSE)	{

        $this->_notificationTo = $mail;
    }

    # display To: Undisclosed recipients

    /**
     * _KMail::mail_list()
     *
     * @return
     */
    public function mail_list()	{

        $this->_mailListMode = TRUE;
    }

    # list id header

    /**
     * _KMail::list_id()
     *
     * @param mixed $str
     * @return
     */
    public function list_id($str)	{

        if ((! isset($text)) or ($this->isEmpty($str)))	return;

        $this->_listIdStr = $str;
    }

    # send list-unsubscribe header

    /**
     * _KMail::unsubscribe()
     *
     * @param bool $mail
     * @param bool $url
     * @return
     */
    public function	unsubscribe($mail=FALSE, $url=FALSE)	{

        if (($mail) && (! $this->isEmpty($mail))) $this->_unsubscribeMail = $mail;

        if (($url) && (! $this->isEmpty($url))) $this->_unsubscribeUrl = $url;
    }

    # display senders name

    /**
     * _KMail::sender_name()
     *
     * @param mixed $name
     * @return
     */
    public function sender_name($name)	{

        if (! $this->isEmpty($name)) $this->_sender = $name;
    }

    # send as plain text

    /**
     * _KMail::txt()
     *
     * @return
     */
    public function txt()	{

        $this->_text = TRUE;
    }

    /**
     * _KMail::subject()
     *
     * @param mixed $subject
     * @return
     */
    public function subject($subject)	{

        if ($this->isEmpty($subject)) return;

        # strip invalid chars, leave space and tab

        $this->_subject = mb_eregi_replace('\r|\n|\a|\e|f|\v', '', $subject, 'm');
    }

    # message

    /**
     * _KMail::message()
     *
     * @param mixed $message
     * @return
     */
    public function message($message)	{

        $this->_message = $message;
    }

    # read message from file

    /**
     * _KMail::message_from_file()
     *
     * @param mixed $file
     * @return
     */
    public function message_from_file($file)	{

        $this->_messageFile = $file;
    }

    # add attachments

    /**
     * _KMail::attach()
     *
     * @param mixed $files
     * @return
     */
    public function attach($files)	{

        if (! isset($files))	return;

        if (! is_array($files))	{

            if ($this->isEmpty($files)) return;

            $files = $this->commaSepExplode($files);
        }

        $this->_files = $files;
    }

    # add embedd pictures into html

    /**
     * _KMail::embedd_pics()
     *
     * @param mixed $pictures
     * @return
     */
    public function embedd_pics($pictures)	{

        if (! isset($pictures))	return;

        if (! is_array($pictures))	{

            if (! strlen($pictures)) return;

            $pictures = $this->commaSepExplode($pictures);
        }

        $this->_embedd=$pictures;
    }

    # returns report

    /**
     * _KMail::report()
     *
     * @return
     */
    public function report()	{

        if (! $this->_reportStr) $this->_reportStr = "\n<br>Mail succesfuly has been send without any warning or error.";

        return $this->_reportStr;
    }

    # extends mbstring functionality

    /**
     * _KMail::mb_ereg_match_str()
     *
     * @param mixed $pattern
     * @param mixed $subject
     * @param string $match
     * @param string $option
     * @return
     */
    private function mb_ereg_match_str($pattern, $subject, &$match='', $option='')	{

        if (! mb_ereg_search_init($subject, $pattern, $option)) return FALSE;

        $r = mb_ereg_search_regs();

        $match = $r[0];

        if ($match==NULL) return FALSE;

        return TRUE;
    }

    # extends mbstring functionality

    /**
     * _KMail::mb_ereg_match_all()
     *
     * @param mixed $pattern
     * @param mixed $subject
     * @param mixed $matches
     * @param string $option
     * @return
     */
    private function mb_ereg_match_all($pattern, $subject, &$matches=array(), $option='')	{

        if (! mb_ereg_search_init($subject, $pattern, $option)) return FALSE;

        $matches = array();

        while($r = mb_ereg_search_regs()) $matches[] = $r;

        if ($matches==NULL) return FALSE;

        return TRUE;
    }

    # strips invalid chars from string

    /**
     * _KMail::stripInvalidChars()
     *
     * @param mixed $str
     * @return
     */
    private function stripInvalidChars($str)	{

        return mb_eregi_replace('\r|\n|\t|\a|\e|\f|\v|\s', '', $str, 'm');
    }

    # checks the string is '' or not

    /**
     * _KMail::isEmpty()
     *
     * @param mixed $str
     * @return
     */
    private function isEmpty($str)	{

        if ((! is_numeric($str)) && (! $str)) return TRUE;

        return FALSE;
    }

    # explode utf-8 comma separated list to array

    /**
     * _KMail::commaSepExplode()
     *
     * @param mixed $string
     * @return
     */
    private function commaSepExplode($string)	{
        //inicia cambios multifacturas
        $array = mb_split(',', $string);

        foreach ($array as $key => $val)	{

            $array[$key] = trim($this->stripInvalidChars($val));
        }
        //finaliza cambios multifacturas
        return $array;
    }

    # implode mail adresses into comma separated list

    /**
     * _KMail::commaSepImplode()
     *
     * @param mixed $mails
     * @return
     */
    private function commaSepImplode($mails)	{

        if ((! isset($mails)) or (! is_array($mails)))	return '';

        $commaSepList = '';

        $count = count($mails);

        $inc = 1;

        foreach ($mails as $mail)	{

            $commaSepList .= $mail;

            if ($inc != $count)	{

                $commaSepList.=', ';

                $inc++;
            }
        }

        # wrap the list to not be to long, shorter because of firts line

        return wordwrap($commaSepList, 68, $this->_EOL.$this->_WSP);
    }

    # adds line to report

    /**
     * _KMail::addToReport()
     *
     * @param mixed $line
     * @return
     */
    private function addToReport($line)	{

        $this->_reportStr .= $line."<br>\n";
    }

    # return file mime-type

    /**
     * _KMail::getMimeType()
     *
     * @param mixed $file
     * @return
     */
    private function getMimeType($file)	{

        $mime = new MimeType();

        return $mime->get($file);
    }

    # read html from file or url

    /**
     * _KMail::readHtmlFile()
     *
     * @return
     */
    private function readHtmlFile()	{

        $this->_message = @file_get_contents($this->_messageFile);

        if ($this->_message === FALSE)	{

            $this->addToReport("Message file $this->_messageFile not found or is not readable.");

            return FALSE;
        }

        return TRUE;
    }

    # quoted printable encode subject

    /**
     * _KMail::encodeSubject()
     *
     * @param mixed $subject
     * @return
     */
    private function encodeSubject($subject)	{

        if ($this->isEmpty($subject))	return '';

        # split to chars

        $chars		=	array();
        $len		=	mb_strlen($subject);

        for ($i=0; $i<$len; $i++) $chars[] = mb_substr($subject, $i, 1);

        $subjectC	=	'';
        $part	 	=	'';

        # encode

        $strLen		=	1;
        $allowed	=	21;	# first line
        $stepOver	=	FALSE;

        for ($i=0; $i<$len; $i++)	{

            $part .= $chars[$i];

            if (($strLen > $allowed) or ($i==$len-1))	{

                # wait for the first space

                if (($i==($len-1)) or ($chars[$i]===' ') or ($strLen>50))	{

                    # hard way break the word apart

                    $subjectC .= "=?UTF-8?Q?".quoted_printable_encode($part)."?=".$this->_EOL;

                    if ($i != ($len-1)) $subjectC .= $this->_WSP;

                    $part		=	'';
                    $strLen		=	1;
                    $allowed	=	37;	# after first line
                    $stepOver	=	TRUE;
                }
            }

            if (! $stepOver) {$strLen += strlen($part);} else {$stepOver = FALSE;}
        }
        return $subjectC;
    }

    # replace image tags in html with cid-s

    /**
     * _KMail::insertCids()
     *
     * @return
     */
    private function insertCids()	{

        $inlineList = array();

        $cidInc = 1;

        # first delete </img> tags if there is

        $this->_message = mb_eregi_replace('\<\s*\/\s*img.\s*\/\>', '', $this->_message, 'm');

        # the get the list if img tags

        $count = 0;

        if ($this->mb_ereg_match_all('\<\s*img\s.*?\>', $this->_message, $imgTags, 'im')) $count = count($imgTags);

        if ((! $count) && ($this->_embedd))	{

            $this->addToReport('Images defined for embedding not exists in message.');

            return FALSE;
        }

        # LIMITATION: 	cannot use different pictures with same filename and different
        #  		path the routine will use only the first picture for all

        $now = time();

        for ($inc=0; $inc<$count; $inc++)	{

            $tag = $imgTags[$inc][0];

            $this->mb_ereg_match_str('src\s*=\s*\"(.*?)\"', $tag, $src, 'im');

            $filename = mb_eregi_replace('^src\s*=\s*\"|\"$','',$src, 'm');

            $founded = FALSE;

            foreach ($this->_embedd as $inline)	{

                # delete from message if not found

                if (! is_readable($inline)) continue;

                # get the filename

                $parts		=	mb_split('/', $inline);
                $picCount	=	count($parts);
                $pic		=	$parts[$picCount-1];

                if ((isset($pic)) && (mb_strpos($filename, $pic) !== FALSE))	{

                    # create new src with cid

                    $cid = 'src="cid:img'.$cidInc.$now.'"';

                    # replace in html

                    $this->_message = mb_ereg_replace($src, $cid, $this->_message, 'm');

                    # collect into array $cid=>$embedd

                    $inlineList = array_merge($inlineList, array('img'.$cidInc.$now=>$inline));

                    $cidInc++;

                    $founded=TRUE;
                }
            }

            if (! $founded) {

                $this->addToReport('Image file defined in '.htmlspecialchars($tag).' tag not found.');

                # delete not founded picture from message

                $this->_message = mb_ereg_replace($tag, '<br>', $this->_message, 'm');
            }
        }

        if (count($inlineList))	return $inlineList;

        return FALSE;
    }

    # write unscrubscribe header

    /**
     * _KMail::unsubscribe_list()
     *
     * @return
     */
    private function unsubscribe_list()	{

        $unsubscribe	=	'List-Unsubscribe: ';

        if ($this->_unsubscribeMail) $unsubscribe.='<mailto:'.$this->stripInvalidChars($this->_unsubscribeMail).'>';

        if (($this->_unsubscribeUrl) && ($this->_unsubscribeMail)) $unsubscribe.=','.$this->_EOL.$this->_WSP;

        if ($this->_unsubscribeUrl) $unsubscribe.='<'.$this->stripInvalidChars($this->_unsubscribeUrl).'>';

        return	$unsubscribe;
    }

    # compose header

    /**
     * _KMail::header()
     *
     * @return
     */
    private function header()	{

        $header	=	'';
        $EOL	=	$this->_EOL;

        # determine (server) host

        if (! $mailerHost = $_SERVER['HTTP_HOST'])	{

            if (! $mailerHost = $_SERVER['SERVER_NAME']) $mailerHost = 'localhost';
        }

        # basic header

        if (isset($this->_replyTo))	{

            # reply-to

            if (! $this->_sender)	$this->_sender = mb_eregi_replace('@(.*?)$','', $this->_from, 'm');

            if ($this->_replyTo)	{$replyto = $this->_replyTo;} else {$replyto = $this->_from;}

            $header		= 	 "From: =?UTF-8?Q?".quoted_printable_encode($this->_sender)."?= <$this->_from>".$EOL
                ."Reply-To: =?UTF-8?Q?".quoted_printable_encode($this->_sender)."?= <$replyto>".$EOL
                ."Return-Path: =?UTF-8?Q?".quoted_printable_encode($this->_sender)." <$this->_from>".$EOL;
        }	else	{

            # no reply

            if ($this->_sender)	{

                $senderName = '=?UTF-8?Q?'.quoted_printable_encode($this->_sender).'?=';

            }	else	{

                $senderName = $mailerHost;
            }

            $noreply	=	$senderName.' <no-reply@'.str_replace('www.','', strtolower($mailerHost)).'>';

            $header		= 	 "From:  $noreply".$EOL
                ."Return-Path: $noreply".$EOL;
        }

        # add carbon copy and blind carbon copy

        if ((count($this->_recipientsList) > 1) && (! $this->_mailListMode))	{

            if (! isset($this->_cc)) $this->_cc = FALSE;

            if (! isset($this->_bcc)) $this->_bcc = FALSE;

            $header		.=	"To: ".($this->commaSepImplode($this->_to)).$EOL;

            if ($this->_bcc)	{

                $header	.=	"Bcc: ".$EOL;
            }

            if ($this->_cc)	{

                # cc is send check for address in to not appear twice

                foreach (array_keys($this->_cc) as $key)	{

                    if (in_array($this->_cc[$key], $this->_to))	unset($this->_cc[$key]);
                }

                $header	.=	"Cc: ".($this->commaSepImplode($this->_cc)).$EOL;
            }

        }	else	{

            if ($this->_mailListMode)	{

                $header	.=	"To: Undisclosed Recipients <$this->_from>".$EOL;

            }	else	{

                $header	.=	"To: <$this->_primaryTo>".$EOL;
            }
        }

        if ($this->_subject)	{

            $subjectC	=	"Subject: =?UTF-8?Q?".quoted_printable_encode($this->_subject).'?='.$EOL;

            if (strlen($subjectC) > 74)	{

                # in most cases this will step over - wrap the subject

                $subjectC = 'Subject: '.$this->encodeSubject($this->_subject);

            }
            $header 	.=	 $subjectC;


        }	else	{

            # send empty subject header if subject is not defined

            $header		.=	 'Subject: '.$EOL;
        }


        //inicia cambios multifacturas
        $date_x = date(DATE_RFC2822);
        $header 		."Message-ID: <".time()." KMail@".str_replace('www.','', strtolower($mailerHost)).">".$EOL;
        $header 		.=	 "Date: $date_x".$EOL;
        //finaliza cambios multifacturas
        # add List_id & Unsubscribe header

        if ($this->_mailListMode)	{

            $header		.=	'Precedence: bulk'.$EOL;

            if ($this->_listIdStr)	{

                $header	.=	'List-Id: =?UTF-8?Q?'.quoted_printable_encode($this->_listIdStr).'?='.$EOL.$this->_WSP.'<'.$mailerHost.'>'.$EOL;
            }

            if (($this->_unsubscribeUrl) or ($this->_unsubscribeMail))	{

                $header	.=	$this->unsubscribe_list().$EOL;
            }
        }

        if ($this->_notificationTo) $header .= "Disposition-Notification-To: $this->_notificationTo";

        $header			.=	 'X-Mailer: KMail PHP v'.phpversion().$EOL
            .'MIME-Version: 1.0'.$EOL;

        $this->header=$header;
    }

    # covert html to txt

    /**
     * _KMail::html2txt()
     *
     * @param mixed $html
     * @return
     */
    private function html2txt($html)	{

        if ($html === '') return $html;

        # replace the images with alt or filename

        $this->_message = mb_eregi_replace('\<\s*\/\s*img.\s*\>', '', $html, 'm');

        $this->mb_ereg_match_all('\<\s*img\s.*?\>', $html, $imgTags, 'im');

        $count = count($imgTags);

        for ($inc=0; $inc<$count; $inc++)	{

            $part=$imgTags[$inc][0];

            if ($this->mb_ereg_match_str('alt\s*=\s*\"(.*?)\"', $part, $altTag, 'im'))	{

                $altText = mb_eregi_replace('^alt\s*=\s*\"|\"$','',$altTag, 'm');

            }	else	{

                $altText = '';

                # get the filename

                if ($this->mb_ereg_match_str('src\s*=\s*\"(.*?)\"', $part, $src, 'i'))	{

                    $cid = mb_eregi_replace('^src\s*=\s*\"\s*cid:\s*|\"$','',$src, 'm');

                    $parts = mb_split('/', $this->_inlines[$cid]);

                    $src_count = count($parts);

                    $altText = $parts[$src_count-1];
                }
            }

            $html = mb_ereg_replace($part, $altText, $html, 'm');
        }

        # remove header

        $this->mb_ereg_match_str('\<\s*body\s*\>.*?\<\s*\/body\s*\>', $html, $html, 'im');

        # strip

        $html = strip_tags($html, '<br>');  #for strip_tags "<br/>" & "<br>" is the same

        $html = $this->stripInvalidChars($html); # strip unvanted

        $html = mb_eregi_replace('\<\s*br\s*\/*\>', $this->_EOL, $html, 'm'); # change to eol

        $html =  mb_eregi_replace("\n\n", "\n", $html, 'm'); # remove duplicate line breaks

        return $html;
    }

    # compose text/plain

    /**
     * _KMail::plain_text()
     *
     * @return
     */
    private function plain_text()	{

        $EOL=$this->_EOL;

        $this->body		= "Content-Type: text/plain; charset=\"UTF-8\"".$EOL
            ."Content-Transfer-Encoding: quoted-printable".$EOL.$EOL

            .quoted_printable_encode($this->html2txt($this->_message)).$EOL.$EOL;
    }

    # compose multipart alternative

    /**
     * _KMail::multipartAlternative()
     *
     * @return
     */
    private function multipartAlternative()	{

        $EOL	=	$this->_EOL;
        $WSP	=	$this->_WSP;

        $ALTSEP	=	md5(time()+10);

        $this->body	 =  'Content-Type: multipart/alternative;'.$EOL.$WSP."boundary=\"$ALTSEP\"".$EOL.$EOL
            ."--".$ALTSEP.$EOL

            # text part

            ."Content-Type: text/plain; charset=\"UTF-8\"".$EOL
            ."Content-Transfer-Encoding: quoted-printable".$EOL.$EOL
            .quoted_printable_encode($this->html2txt($this->_message)).$EOL.$EOL  // strip html tags alt stands
            ."--".$ALTSEP.$EOL

            # html & relative part

            ."Content-Type: text/html; charset=\"UTF-8\"".$EOL
            ."Content-Transfer-Encoding: quoted-printable".$EOL.$EOL
            .quoted_printable_encode($this->_message).$EOL.$EOL
            ."--".$ALTSEP."--".$EOL.$EOL; //END
    }

    # compose multipart related

    /**
     * _KMail::multipartRelated()
     *
     * @return
     */
    private function multipartRelated()	{

        $EOL	=	$this->_EOL;
        $WSP	=	$this->_WSP;

        $RELSEP	=	md5(time()+100);

        # check for existens

        if ($this->_embedd)	{

            foreach ($this->_inlines as $cid=>$inline)	{

                if (! is_readable($inline))	{

                    $this->addToReport("File not found or file is not readable: $inline .");

                    unset($this->_inlines[$cid]);
                }
            }

            if (! count($this->_inlines)) $inlines=FALSE;
        }

        if (! $this->_inlines)	return;

        $this->body ='Content-Type: multipart/related;'.$EOL.$WSP.
            "boundary=\"$RELSEP\";".$EOL.$EOL
            ."--".$RELSEP.$EOL
            .$this->body;

        # add inline pictures

        foreach ($this->_inlines as $cid=>$inline)	{

            $filename = basename($inline);

            $mimetype = $this->getMimeType($inline);

            $contentID = str_replace('cid:','',$cid);

            if (! $inline_data = @file_get_contents($inline)) continue;

            $this->body .=	 "--".$RELSEP.$EOL
                ."Content-Type: $mimetype;".$EOL.$WSP."name=\"$filename\"".$EOL
                ."Content-Transfer-Encoding: base64".$EOL
                ."Content-ID: <$contentID>".$EOL
                ."X-Attachment-Id: $contentID".$EOL
                ."Content-Disposition: INLINE".$EOL.$EOL
                .(chunk_split(base64_encode($inline_data))).$EOL.$EOL;
        }

        $this->body  .=  "--".$RELSEP."--".$EOL.$EOL;
    }

    # compose multipart mixed

    /**
     * _KMail::multipartMixed()
     *
     * @return
     */
    private function multipartMixed()	{

        $EOL	=	$this->_EOL;
        $WSP	=	$this->_WSP;

        $MIXSEP	=	md5(time()+200);

        # attachment check if not found step over

        if (count($this->_files))	{

            foreach (array_keys($this->_files) as $key)	{

                if (! is_readable($this->_files[$key]))	{

                    $this->addToReport('File not found or file is not readable: '.$this->_files[$key].' .');

                    unset($this->_files[$key]);
                }
            }
        }

        if (! count($this->_files)) return;

        # additional header to show attachment sign in email client

        $this->body	  = 	'Content-Type: multipart/mixed;'.$EOL.$WSP."boundary=\"$MIXSEP\"".$EOL.$EOL
            ."--".$MIXSEP.$EOL
            .$this->body;

        foreach ($this->_files as $file)		{

            $filename=basename($file);

            $mimetype=$this->getMimeType($file);

            if (! $file_content = @file_get_contents($file)) continue;

            $this->body.=  "--".$MIXSEP.$EOL
                ."Content-Type: $mimetype;".$EOL.$WSP."name=\"$filename\"".$EOL
                ."Content-Transfer-Encoding: base64".$EOL
                ."Content-Disposition: attachment;".$EOL.$WSP."filename=\"$filename\"".$EOL.$EOL
                .(chunk_split(base64_encode($file_content))).$EOL.$EOL;
        }

        $this->body	.=	"--".$MIXSEP."--".$EOL.$EOL; // END
    }

    # compose mail

    /**
     * _KMail::mailCompose()
     *
     * @return
     */
    private function mailCompose()	{

        # create headers

        $this->header();

        # create mail

        if (	($this->_text) or
            (! count($this->_files) && (! $this->_embedd) && ($this->isEmpty($this->_message)))	)	{

            # send as simple text or empty message

            $this->plain_text();

        }	else	{

            # first see the inlines

            $this->_inlines=$this->insertCids();

            # compose alternetive

            $this->multipartAlternative();

            # compose related => add inline pictures

            if ($this->_inlines) $this->multipartRelated();

            # compose mixes => add attachments
            $this->multipartMixed();
        }


        $this->_mailMessage=$this->header.$this->body;

        unset($this->header, $this->body);
    }

    # send command to smtp server

    /**
     * _KMail::commandSend()
     *
     * @param mixed $command
     * @param mixed $expectedCode
     * @return
     */
    private function commandSend($command, $expectedCode)	{

        if ((! $command) or (! $expectedCode) or (! $this->_smtp)) return FALSE;

        if (! @fputs($this->_smtp, $command)) return FALSE;

        $response	=	$this->getResponse();
        $responseOK	=	FALSE;

        # debug

        if ($this->_debug) echo "<br>Command:<br>".nl2br(htmlspecialchars($command))."<br>Response:".nl2br($response)."\n";

        if ($response)	{

            # check for authorization

            if ((strpos($command, 'EHLO') !== FALSE) or (strpos($command, 'HELO') !== FALSE)) 	{

                if (strpos(strtoupper($response), 'AUTH') !== FALSE) $this->_serverAuth = TRUE;
            }

            # response code

            $code=substr($response, 0, 3);

            if (! is_array($expectedCode))	$expectedCode = array($expectedCode);

            if (in_array($code, $expectedCode)) $responseOK=TRUE;
        }

        if (! $responseOK)	{

            if (! $response)	{

                $this->addToReport("SMTP Server not responding.");

            }	else	{

                $this->addToReport("Invalid response code: <br>$response");
            }

            return FALSE;
        }

        return TRUE;
    }

    # read response from smtp server

    /**
     * _KMail::getResponse()
     *
     * @return
     */
    private function getResponse()	{

        if (	(! $this->_smtp) or
            (! $response=@fread($this->_smtp, 1)) or
            (! $state = socket_get_status($this->_smtp))	) return FALSE;

        if ($state['timed_out']) {

            $this->addToReport('Connection response timeout.');
            return FALSE;
        }

        if (! $left = @fread($this->_smtp, $state['unread_bytes'])) return FALSE;

        return $response.=$left;
    }

    # connect to smtp server

    /**
     * _KMail::connect()
     *
     * @return
     */
    private function connect()	{

        $this->_smtp = @fsockopen($this->_secure.$this->_host, $this->_port, $errno, $errstr, 30);

        if ((! $this->_smtp) && ($this->_secure == 'tls://'))	{

            # first check if tls connection is then try to initialize with STARTTLS

            if ($this->_smtp = @fsockopen($this->_host, $this->_port, $errno, $errstr, 30)) $this->_STARTTLS = TRUE;
        }

        if (! $this->_smtp) {

            $this->addToReport("Can not connect to host: $this->_secure$this->_host: $this->_port. ($errstr)");

            return FALSE;
        }

        # if connection is established get and set time limit

        $this->_timeLimit = ini_get('max_execution_time');

        set_time_limit(0);

        return TRUE;
    }

    # disconnect from smtp server

    /**
     * _KMail::disconnect()
     *
     * @param bool $err
     * @return
     */
    private function disconnect($err=FALSE)	{

        # if some error happend send reset and quit commands

        if ($err)	{

            @fputs($this->_smtp, 'RSET'.$this->_EOL);
            @fputs($this->_smtp, 'QUIT'.$this->_EOL);
        }

        @fclose($this->_smtp);

        if ($this->_debug)	echo "\n<br>Disconnected.";

        # restore time limit

        set_time_limit($this->_timeLimit);
    }

    # send mail

    /**
     * _KMail::send()
     *
     * @return
     */
    public function send()	{

        if ((! $this->_host) or (! $this->_port))	{

            $this->addToReport('Invalid smtp host or port defined.');

            return FALSE;
        }

        if (	(($this->isEmpty($this->_username)) && (! $this->isEmpty($this->_password)))	or
            ((! $this->isEmpty($this->_username))	&& ($this->isEmpty($this->_password)))	)	{

            $this->addToReport('Invalid user name or password defined.');

            return FALSE;
        }

        # added in ver 1.3 read the message file if is defined

        if ($this->_messageFile)	{

            if (! $this->readHtmlFile()) return FALSE;
        }

        if (($this->isEmpty($this->_subject))	&& ($this->isEmpty($this->_message)) && (! count($this->_files)))	{

            $this->addToReport('Nothing to send.');

            return FALSE;
        }

        if (! $this->_recipientsList = $this->_to)	{

            $this->addToReport('One recipient to(\'recipient@mail\') must to be defined.');

            return FALSE;
        }

        if (count($this->_bcc)) $this->_recipientsList = array_merge($this->_recipientsList, $this->_bcc);

        if (count($this->_cc))	$this->_recipientsList = array_merge($this->_recipientsList, $this->_cc);

        # remove duplicates

        $this->_recipientsList = array_unique($this->_recipientsList);

        # write the message

        $this->mailCompose();

        if (! $this->_mailMessage) return FALSE;

        $EOL = $this->_EOL;

        # send the mails in chunks of defult session limit number of recipients

        $this->_recipientsList = array_chunk($this->_recipientsList, $this->_sessionLimit);

        ob_start();

        $serverError = FALSE;

        foreach ($this->_recipientsList as $this->_to)	{

            # smtp connect

            if (! $this->connect())	{

                $serverError='con'; # to make difference if cannot make the connection

                break;
            }

            # empty the first response

            if (! $this->getResponse())	{

                $serverError=TRUE;

                break;
            }

            # say hello

            if (! $this->commandSend('EHLO '.$_SERVER['SERVER_NAME'].$EOL, 250))	{

                if (! $this->commandSend('HELO '.$_SERVER['SERVER_NAME'].$EOL, 250)) {

                    $serverError=TRUE;

                    break;
                }
            }

            # initialize tls connection by need

            if ($this->_STARTTLS)	{

                if (! $this->commandSend('STARTTLS'.$EOL, 220)) {

                    $serverError=TRUE;

                    break;

                }	else	  {

                    stream_socket_enable_crypto($this->_smtp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
                }
            }

            # authorize

            if (($this->_serverAuth) && ($this->isEmpty($this->_username) or ($this->isEmpty($this->_password))))	{

                $this->addToReport('Server require authentication.');

                $serverError=TRUE;

                break;
            }

            if (strlen($this->_username))	{

                if (! $this->commandSend("AUTH LOGIN".$EOL, 334))	{

                    $serverError=TRUE;

                    break;
                }

                if (! $this->commandSend(base64_encode($this->_username).$EOL, 334))	{

                    $serverError=TRUE;

                    break;
                }

                if (! $this->commandSend(base64_encode($this->_password).$EOL, 235))	{

                    $serverError=TRUE;

                    break;
                }
            }

            # from

            if (! $this->commandSend("MAIL FROM:<$this->_from>".$EOL, 250))	{

                $serverError=TRUE;

                break;
            }

            # to

            foreach ($this->_to as $send_to)	{

                if (! $this->commandSend("RCPT TO:<$send_to>".$EOL, array(250, 251, 450, 550, 551, 553)))	{

                    $serverError=TRUE;

                    break 2;
                }

                usleep((integer)($this->_rcpDelay*1000000));
            }

            # message

            if (! $this->commandSend("DATA".$EOL, 354))	{

                $serverError=TRUE;

                break;
            }

            if (! $this->commandSend($this->_mailMessage.$EOL.'.'.$EOL, 250))	{

                $serverError=TRUE;

                break;
            }

            # quit

            if (! $this->commandSend("QUIT".$EOL, 221)) {

                $serverError=TRUE;

                break;
            }

            # smtp diconnect

            $this->disconnect();

            sleep($this->_conDelay);
        }

        ob_end_flush();

        if ($this->INTERNAL_ENC) mb_internal_encoding($this->INTERNAL_ENC);

        if ($this->REGEX_ENC) mb_regex_encoding($this->REGEX_ENC);

        if ($serverError)	{

            # try to send reset to server and quit properly

            if ($serverError!='con') $this->disconnect(TRUE);

            return FALSE;
        }

        $this->addToReport('OK');

        return TRUE;
    }
}