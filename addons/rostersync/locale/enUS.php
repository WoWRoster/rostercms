<?php

// Button names
$lang['rsync_button1']	= 'Character Update|Synchronize your character with Blizzard\'s Armory';
$lang['rsync_button2']	= 'Guild Members Update|Synchronize your guild\'s characters with Blizzard\'s Armory';
$lang['rsync_button3']	= 'Add A Guild|Update your guild with Blizzard\'s Armory';
$lang['rsync_button4']	= 'Memberslist Update|Update your memberlist with Blizzard\'s Armory';

$lang['guildleader'] = 'Guild Master';

$lang['admin']['rsync_conf']			= 'RosterSync Config';
$lang['admin']['rsync_ranks']			= 'Sync ranks';
$lang['admin']['rsync_scaning']			= 'Scanning settings';
$lang['admin']['rsync_scan_guild']		= 'Guild Fields';
$lang['admin']['rsync_scan_char']		= 'Char Fields';
$lang['admin']['rsync_access']			= 'Update Access';
$lang['admin']['rsync_debug']			= 'Debug Settings';

$lang['admin']['rsync_host']			= 'Host|Host to Synchronize with';
$lang['admin']['rsync_minlevel']		= 'Minimum Level|Minimum level of characters to synchronize<br>Currently this should be no lower than 10 since<br>the armory doesn\'t list characters lower than level 10';
$lang['admin']['rsync_synchcutofftime']	= 'Sync cutoff time|Time in days<br>All characters not updated in last (x) days will be synchronized';
$lang['admin']['rsync_skip_cache']		= 'Skip Cache and LMH|Whether to use LMH or cache data for api calls.';
$lang['admin']['rsync_reloadwaittime']	= 'Reload wait time|Time in seconds<br>Time in seconds before next synchronization during a sync job 24+ recommended';
$lang['admin']['rsync_fetch_timeout'] 	= 'Armory Fetch timeout|Time in seconds till a fetch of a single XML file is aborted.';
$lang['admin']['rsync_skip_start']		= 'Skip start page|Skip start page on Roster Sync updates.';
$lang['admin']['rsync_status_hide'] 	= 'Hide status windows initially|Hide the status windows of Roster Sync on the first load.';
$lang['admin']['rsync_protectedtitle']	= 'Protected Guild Title|Characters with these guild titles are protected<br />from being deleted by a synchronization against the armory.<br />This problem often occours with bank characters.<br />Multiple values seperated by a comma (,) \"Banker,Stock\"';
$lang['admin']['rsync_ajax_key']		= 'Ajax Key|This key tells armory sync that u are authorised to process members without logging in';

$lang['admin']['rsync_scaning']			= 'Scaning Settings';
$lang['admin']['rsync_MinLvl']			= 'Min Level';
$lang['admin']['rsync_MaxLvl']			= 'Max Level';
$lang['admin']['rsync_Rank']			= 'Ranks';
$lang['admin']['rsync_Class']			= 'Classes';

$lang['admin']['rsync_rank_set_order']	= "Guild Rank Set Order|Defines in which order the guild titles will be set.";
$lang['admin']['rsync_rank_0']			= "Title Guild Leader|This title will be set if in WoWRoster for that guild none is defined.";
$lang['admin']['rsync_rank_1']			= "Title Rank 1|This title will be set if in WoWRoster for that guild none is defined.";
$lang['admin']['rsync_rank_2']			= "Title Rank 2|This title will be set if in WoWRoster for that guild none is defined.";
$lang['admin']['rsync_rank_3']			= "Title Rank 3|This title will be set if in WoWRoster for that guild none is defined.";
$lang['admin']['rsync_rank_4']			= "Title Rank 4|This title will be set if in WoWRoster for that guild none is defined.";
$lang['admin']['rsync_rank_5']			= "Title Rank 5|This title will be set if in WoWRoster for that guild none is defined.";
$lang['admin']['rsync_rank_6']			= "Title Rank 6|This title will be set if in WoWRoster for that guild none is defined.";
$lang['admin']['rsync_rank_7']			= "Title Rank 7|This title will be set if in WoWRoster for that guild none is defined.";
$lang['admin']['rsync_rank_8']			= "Title Rank 8|This title will be set if in WoWRoster for that guild none is defined.";
$lang['admin']['rsync_rank_9']			= "Title Rank 9|This title will be set if in WoWRoster for that guild none is defined.";


/*
Player scan settings
*/
$lang['admin']['rsync_char_achievements']	= 'Achievements|A map of achievement data including completion timestamps and criteria information.';
$lang['admin']['rsync_char_appearance']		= 'Appearance|A map of values that describes the face, features and helm/cloak display preferences and attributes.';
$lang['admin']['rsync_char_feed']			= 'Activity Feed|The activity feed of the character.';
$lang['admin']['rsync_char_guild']			= 'Guild Data|A summary of the guild that the character belongs to. If the character does not belong to a guild and this field is requested, this field will not be exposed.';
$lang['admin']['rsync_char_hunterPets']		= 'Hunter Pets|A list of all of the combat pets obtained by the character.';
$lang['admin']['rsync_char_items']			= 'Equipment|A list of items equipted by the character. Use of this field will also include the average item level and average item level equipped for the character.';
$lang['admin']['rsync_char_mounts']			= 'Mounts|A list of all of the mounts obtained by the character.';
$lang['admin']['rsync_char_pets']			= 'Pets|A list of the battle pets obtained by the character.';
$lang['admin']['rsync_char_petSlots']		= 'Battle Pets|Data about the current battle pet slots on this characters account.';
$lang['admin']['rsync_char_professions']	= 'professions|A list of the character\'s professions. It is important to note that when this information is retrieved, it will also include the known recipes of each of the listed professions.';
$lang['admin']['rsync_char_progression']	= 'Raid Progress|A list of raids and bosses indicating raid progression and completedness.';
$lang['admin']['rsync_char_pvp']			= 'PvP|A map of pvp information including arena team membership and rated battlegrounds information.';
$lang['admin']['rsync_char_quests']			= 'Quest data|A list of quests completed by the character.';
$lang['admin']['rsync_char_reputation']		= 'Reputation|A list of the factions that the character has an associated reputation with.';
$lang['admin']['rsync_char_stats']			= 'Stats|A map of character attributes and stats.';
$lang['admin']['rsync_char_talents']		= 'Talents|A list of talent structures.';
$lang['admin']['rsync_char_titles']			= 'Titles|A list of the titles obtained by the character including the currently selected title.';
$lang['admin']['rsync_char_audit']			= 'Audit|Raw character audit data that powers the character audit on the game site';
/*
	guild scan settings
*/
$lang['admin']['rsync_guild_members']		= 'Members|A list of characters that are a member of the guild';
$lang['admin']['rsync_guild_achievements']	= 'Achievements|A set of data structures that describe the achievements earned by the guild.';
$lang['admin']['rsync_guild_news']			= 'News|A set of data structures that describe the news feed of the guild.';
$lang['admin']['rsync_guild_challenge']		= 'Challenges|The top 3 challenge mode guild run times for each challenge mode map.';

/*
Debug Info
*/
$lang['admin']['rsync_debuglevel']		= 'Debug Level|Adjust the debug level for Roster Sync.<br /><br />Quiete - No Messages<br />Base Info - Base messages<br />Armory & Job Method Info - All messages of Armory and Job methods<br />All Methods Info - Messages of all Methods  <b style="color:red;">(Be careful - very much data)</b>';
$lang['admin']['rsync_debugdata']		= 'Debug Data|Raise debug output by methods arguments and returns<br /><b style="color:red;">(Be careful - much more info on high debug level)</b>';
$lang['admin']['rsync_javadebug']		= 'Debug Java|Enable JavaScript debugging.<br />Not implemented yet.';
$lang['admin']['rsync_xdebug_php']		= 'XDebug Session PHP|Enable sending XDEBUG variable with POST.';
$lang['admin']['rsync_xdebug_ajax']		= 'XDebug Session AJAX|Enable sending XDEBUG variable with AJAX POST.';
$lang['admin']['rsync_xdebug_idekey']	= 'XDebug Session IDEKEY|Define IDEKEY for Xdebug sessions.';
$lang['admin']['rsync_sqldebug']		= 'SQL Debug|Enable SQL debugging for Roster Sync.<br />Not useful in combination with roster SQL debugging / duplicate data.';
$lang['admin']['rsync_updateroster']	= "Update Roster|Enable roster updates.<br />Good for debugging<br />Not implemented yet.";


/*
update access
*/
$lang['admin']['rsync_char_update_access'] 				= 'Char Update Access|Who is able to do character updates';
$lang['admin']['rsync_guild_update_access'] 			= 'Guild Update Access|Who is able to do guild updates';
$lang['admin']['rsync_guild_memberlist_update_access'] 	= 'Guild Memberlist Update Access|Who is able to do guild memberlist updates';
$lang['admin']['rsync_realm_update_access'] 			= 'Realm Update Access|Who is able to do realm updates';
$lang['admin']['rsync_guild_add_access'] 				= 'Guild Add Access|Who is able to add new guilds';

$lang['start'] = "Start";
$lang['start_message'] = "You're about to start Roster Sync for %s.<br><br>By doing this, all data for %s will be overwritten<br />with details from Blizzard Api.<br /><br />Do you want to start this process yet?";

$lang['start_message_the_char']			= "the character";
$lang['start_message_this_char']		= "this character";
$lang['start_message_the_guild']		= "the guild";
$lang['start_message_this_guild']		= "all characters of this guild";
$lang['start_message_the_memberlist']	= "the Guild Memberslist";
$lang['start_message_this_memberlist']	= "the guild memberslist";

$lang['start_message_the_profile']		= "Profiles";
$lang['start_message_the_gprofile']		= "Guild profiles";
$lang['start_message_the_addguild']		= "Adding a Guild";
$lang['start_message_the_memberlist']	= "Guild Members List";

$lang['start_message_this_profile']		= "this character";
$lang['start_message_this_gprofile']	= "the guild characters";
$lang['start_message_this_memberlist']	= "the guild members";
$lang['start_message_this_addguild']	= "Guild data for this guild";

$lang['misc']['Rank'] = "Rank";
$lang['guild_short'] = "Guild";
$lang['character_short'] = "Char";
$lang['skill_short'] = "Skill";
$lang['reputation_short'] = "Rep";
$lang['equipment_short'] = "Equip";
$lang['talents_short'] = "Talent";
$lang['error_log'] = 'Error Log';

$lang['started'] = "Started";
$lang['finished'] = "Finished";

$lang['ApiSyncTitle_Char'] = "ApiSync for Characters";
$lang['ApiSyncTitle_Guild'] = "ApiSync for Guilds";
$lang['ApiSyncTitle_Guildmembers'] = "ApiSync for Guild Member Lists";
$lang['ApiSyncTitle_Realm'] = "ApiSync for Realms";

$lang['next_to_update'] = "Next Update: ";
$lang['nothing_to_do'] = "Nothing to do at the moment";

$lang['error'] = "Error";
$lang['error_no_character'] = "No Character referred.";
$lang['error_no_guild'] = "No Guild referred.";
$lang['error_no_realm'] = "No Realm referred.";
$lang['error_use_menu'] = "Use menu to Synchronize.";

$lang['error_guild_insert'] = "Error creating guild.";
$lang['error_uploadrule_insert'] = "Error creating upload rule.";
$lang['error_guild_notexist'] = "The guild given does not exist in the Armory.";
$lang['error_missing_params'] = "Missing parameters. Please try again";
$lang['error_wrong_region'] = "Invalid region. Only EU and US are valid regions.";
$lang['ApiSync_guildadd'] = "Adding Guild and synchronize<br />memberlist with the Armory.";
$lang['ApiSync_charadd'] = "Adding Character and synchronize<br />with the Armory.";
$lang['ApiSync_add_help'] = "Information";
$lang['ApiSync_add_helpText'] = "Spell the character / guild and the server names exactly how they are listed on the Armory.<br />Region is EU for European and US for American/Oceanic.<br />First, ApiSync will check if the guild exists in the Armory.<br />Next, a synchronization of the memberlist will be done.";

$lang['guildleader'] = "Guildleader";

$lang['rage'] = "Rage";
$lang['energy'] = "Energy";
$lang['focus'] = "Focus";

$lang['ApiSync_credits'] = 'ApiSync Based off of armorysync built on blizzards API.';

$lang['start'] = "Start";

$lang['id_to_faction'] = array(
    "0" => "Alliance",
    "1" => "Horde"
);

$lang['month_to_en'] = array(
    "January" => "January",
    "February" => "February",
    "March" => "March",
    "April" => "April",
    "May" => "May",
    "June" => "June",
    "July" => "July",
    "August" => "August",
    "September" => "September",
    "October" => "October",
    "November" => "November",
    "December" => "December"
);
/*
	rsync roster 3.0
*/

$lang['admin']['rsync_ajaxupdate']		= "Member Sync|Sync the guild members using ajax based on the rules selected";
$lang['admin']['rsync_addguild']		= "Add Guild|used after installing roster to populate with the default guild";
$lang['admin']['rsync_guildupdate']		= "Guild Update|update your guild members list";
$lang['admin']['rsync_playerupdate']	= "Player Update|update a single character";

$lang['admin']['rsync_char_achievements']	= "Achievements|a map of achievement data including completion timestamps and criteria information.";
$lang['admin']['rsync_char_appearance']		= "Appearance|a map of a character's appearance settings such as which face texture they've selected and whether or not a healm is visible.";
$lang['admin']['rsync_char_feed']			= "Feed|the activity feed of the character.";
$lang['admin']['rsync_char_guild']			= "Guild|a summary of the guild that the character belongs to. if the character does not belong to a guild and this field is requested, this field will not be exposed.";
$lang['admin']['rsync_char_hunterPets']		= "Hunter pets|a list of all of the combat pets obtained by the character.";
$lang['admin']['rsync_char_items']			= "Items|a list of items equipped by the character. use of this field will also include the average item level and average item level equipped for the character.";
$lang['admin']['rsync_char_mounts']			= "Mounts|a list of all of the mounts obtained by the character.";
$lang['admin']['rsync_char_pets']			= "Pets|a list of the battle pets obtained by the character.";
$lang['admin']['rsync_char_petSlots']		= "Pet slots|data about the current battle pet slots on this characters account.";
$lang['admin']['rsync_char_professions']	= "Professions|a list of the character's professions. does not include class professions.";
$lang['admin']['rsync_char_progression']	= "Progression|a list of raids and bosses indicating raid progression and completeness.";
$lang['admin']['rsync_char_pvp']			= "Pvp|a map of pvp information including arena team membership and rated battlegrounds information.";
$lang['admin']['rsync_char_quests']			= "Quests|a list of quests completed by the character.";
$lang['admin']['rsync_char_reputation']		= "Reputation|a list of the factions that the character has an associated reputation with.";
$lang['admin']['rsync_char_statistics']		= "Statistics|a map of character statistics.";
$lang['admin']['rsync_char_stats']			= "Stats|a map of character attributes and stats.";
$lang['admin']['rsync_char_talents']		= "Talents|a list of talent structures.";
$lang['admin']['rsync_char_titles']			= "Titles|a list of the titles obtained by the character including the currently selected title.";
$lang['admin']['rsync_char_audit']			= "Audit|raw character audit data that powers the character audit on the game site";