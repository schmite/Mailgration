<HTML>
<HEAD>
<TITLE>IMAP Migration Tool</TITLE>
</HEAD>
<BODY BGCOLOR="#bbccdd">
<BR><BR>
<CENTER>
<FONT SIZE=5 FACE="Comic Sans MS">IMAP Migration Tool</FONT><BR><BR><BR>
<FORM ACTION="archivesetup.php" METHOD="POST">
<FONT FACE="Arial" SIZE=3>
<B>Username and Password to source server</B>
<BR>(NOTE: change the value of src_server)<BR>
<!-- CHANGE THE VALUE OF SRC_SERVER -->
<INPUT TYPE="hidden" NAME="src_server" VALUE="SOURCE_SERVER_HERE">
src_username: <INPUT TYPE="text" NAME="src_username"><BR>
src_password: <INPUT TYPE="password" NAME="src_password"><BR>
<BR>
<B>Username and Password to destination server</B>
<BR>(NOTE: change the value of src_server)<BR>
<!-- CHANGE THE VALUE OF DEST_SERVER -->
<INPUT TYPE="hidden" NAME="dest_server" VALUE="DESTINATION_SERVER_HERE">
dest_username: <INPUT TYPE="text" NAME="dest_username"><BR>
dest_password: <INPUT TYPE="password" NAME="dest_password"><BR>
<BR>
<BR>

<B>Archive or Migrate?</B>
<BR>
<TABLE BORDER="0">
<TR><TD ALIGN="left">

<INPUT type="radio" NAME="inbox_date" VALUE="true">Archive your INBOX messages older than 
<INPUT TYPE="input" NAME="inbox_date_weeks" VALUE="2" SIZE="1"> weeks
<BR>
<INPUT type="radio" NAME="inbox_date" VALUE="false" CHECKED>Migrate Everything in INBOX
<BR><BR>

</TD></TR>
<TR><TD ALIGN="left">

<INPUT TYPE="radio" NAME="folder_date" VALUE="true">Archive messages in your IMAP folders older than 
<INPUT TYPE="input" NAME="folder_date_weeks" VALUE="2" SIZE="1"> weeks
<BR>
<INPUT TYPE="radio" NAME="folder_date" VALUE="false" CHECKED>Migrate Everything in IMAP folders

</TD></TR>
</TABLE>

<BR><BR>

<B>Delete or keep?</B>
<BR>
Do you want to remove the messages from the source server?
<BR>
<INPUT TYPE="radio" NAME="delete_src_msg" VALUE="true">Yes
<INPUT TYPE="radio" NAME="delete_src_msg" VALUE="false" CHECKED>No
<BR><BR>
<INPUT TYPE="submit" VALUE="Write script">
</FONT>
</FORM>

</CENTER>
</BODY>
</HTML>
</BODY>
</HTML>
