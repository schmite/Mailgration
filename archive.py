from imaplib import *
from time import *
from datetime import *

"""
 define the following vars in a file named data.py:
src_host
src_port
src_user
src_pwd
src_mbox
dst_host
dst_user
dst_pwd
"""
from data import *

RECENT = 0
OLD = 1

def main():        
    # Source data
    
    '''
        Limit data
    '''
    date_format = '%d-%m-%Y' 
    
    limit_date_new = datetime.strptime('12-10-2013',date_format) # This data should be imported from the FORM
    limit_date_old = datetime.strptime('12-10-2010',date_format)
    
    # Processing data related to time limits
    today = datetime.today()        
    limit_date_new = min(today,limit_date_new)
    limit_date_old = max(datetime.fromtimestamp(0),limit_date_old)
    # Converting the Data to strings so it can be used as filters
    limit_date_new = strftime('%d-%b-%Y',limit_date_new.timetuple())
    limit_date_old = strftime('%d-%b-%Y',localtime(0))
        
    # Limit number of messages to import
    max_messagesToImport = 10
    directionToImport = RECENT
    '''
        End of Limit Data
    '''
        
    # Setting up a connection with source
    conn = IMAP4(src_host,src_port)
    conn.login(src_user,src_pwd)
        
    # Connecting to desired mailbox and setting number of messages to import
    nMessages = min(max_messagesToImport,numberOfMessagesInMailbox(conn,src_mbox))
    
    # Search every ID and build a list of ids to fetch, applying date filters
    strDateLimit = "(SINCE "+str(limit_date_old)+" BEFORE "+str(limit_date_new)+")"
    
    # Process list         
    result,data = conn.search(None,strDateLimit)    
    ids = data[0] # data is a list.
    id_list = map(int,ids.split()) # ids is a space separated string
    
    # Trimming to the limit of messages to be imported
    if(directionToImport == OLD):
        id_list = id_list[:nMessages]
    elif(directionToImport == RECENT):
        id_list = id_list[-nMessages:]
    print id_list
    formatted_id_list =  str(id_list).strip('[]')
    formatted_id_list= formatted_id_list.replace(" ", "")
    print formatted_id_list
    
    # Fetch the emails
    # obs.: for some reason, IMAP4.fetch returns several 'separator' elements which are useless to the
    # purpose of migration. Upon iterating, we must always iterate every 2 elements instead of
    # every element 
    result, data = conn.fetch(formatted_id_list, "(RFC822)") # fetch the email body (RFC822) for the given ID
    
    # Connecting to destiny
    conn_dest = IMAP4(dst_host)
    conn_dest.login(dst_user,dst_pwd)    
    conn_dest.select(src_mbox)

    # Iterating through mail fetched    
    for data_iterate in data[::2]:
        #print 'Data: '+str(data_iterate)
        conn_dest.append('INBOX.Trash', None, None, data_iterate[1])
    
def numberOfMessagesInMailbox(conn,mbox):
    nMessages = conn.select(mbox)
    if(nMessages[0] == 'OK'):
        return nMessages[1][0]

if __name__ == '__main__': 
    main()