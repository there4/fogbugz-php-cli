# FogBugz Command Line Client

A simple command line application that can be used to manage your FogBugz
account, working on status, read cases, and leave short notes.

## Help
     fb <command> <value> <value>
    
    Information:
     help (command)                :: More information about a task
     recent                        :: Get the five most recent cases you've worked on
     current                       :: Get the number for your current case
     view (#case#)                 :: Get info about the current or a particular case
    
    Editing:
     estimate (#case#) (#hours#)   :: Set the estimate for a case
     note (#case#) ("note string") :: Set a note for a particular case
     start (#case#)                :: Start working on a case
     stop                          :: Stop all work