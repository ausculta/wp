DELIMITER //
CREATE PROCEDURE AddExplorer (
	IN WPID				SMALLINT(2),
    IN TypeID			TINYINT(1),
    IN DateFrom			DATE
)
BEGIN
	INSERT INTO exp1_explorers (ExpWPID, ExpDateStart, ExpStatusID)
    VALUES (WPID, DateFrom, 1);
    IF TypeID > 0
		THEN 
		SELECT LAST_INSERT_ID() INTO @LAST_ID;
		INSERT INTO exp1_exptypes (ExpID, ExpTypeTypeID, DateStart) VALUES (@LAST_ID, TypeID, DateFrom);
	END IF;
END // 
DELIMITER ;

CALL AddExplorer(3, 2, '2020-10-02');

use endvrwpdb1;
DELIMITER //
CREATE PROCEDURE GetExpBadgeID (
	IN SelExpID			SMALLINT(2),
	IN SelBadgeID		SMALLINT(2),
    IN SelDateFrom		DATE
)
BEGIN   
    SELECT COUNT(*) INTO @NOBADGEROWS FROM exp1_expbadges WHERE BadgeID = SelBadgeID AND ExpID = SelExpID;
    IF @NOBADGEROWS > 0 THEN
		SELECT ExpBadgeID INTO @TEMPEXPBADGEID FROM exp1_expbadges WHERE BadgeID = SelBadgeID and ExpID = SelExpID;
	ELSE 
		INSERT INTO exp1_expbadges(ExpID, BadgeID, DateStart) VALUES (SelExpID, SelBadgeID, SelDateFrom);
        SELECT LAST_INSERT_ID() INTO @TEMPEXPBADGEID;	
    END IF;
    
    SELECT @TEMPEXPBADGEID;
END // 
DELIMITER ;


SELECT ExpBadgeID FROM exp1_expbadges WHERE BadgeID = 46 and ExpID = 2
INSERT INTO exp1_expbadges(ExpID, BadgeID, DateStart) VALUES (2, 46, '2020-10-10');
call GetExpBadgeID (2, 46, '2020-10-07')
CALL GetExpBadgeID (2, 38, '2020-10-16')

DELIMITER //
CREATE PROCEDURE GetAllBadges ()
BEGIN
	SELECT B.BadgeID, CONCAT(B.Name, ' (', S.Description, ', ', T.Description, ')'), B.Description 
	FROM exp1_badges B, exp1_badgestatus S, exp1_badgetypes T 
	WHERE T.BadgeTypeID = B.BadgeTypeID AND S.BadgeStatusID = B.BadgeStatusID
	ORDER BY T.Description, B.Description;
END // 
DELIMITER ;


DELIMITER //
CREATE PROCEDURE AddNightAway(
	IN NAExpID			SMALLINT(2),
    IN Days				TINYINT(1),
    IN Description		VARCHAR(50),
    IN Location			VARCHAR(50),
    IN NADateStart		DATE,
    IN NADateEnd		DATE
)
BEGIN
	INSERT INTO exp1_expna (ExpID, NADays, Description , NALocation, DateStart, DateEnd)
    VALUES (NAExpID, Days, Description, Location, NADateStart, NADateEnd);
    
    SELECT SUM(NADays) INTO @TotalNA FROM exp1_expna WHERE ExpID = NAExpID;
    
    UPDATE exp1_explorers SET TotalNightsAway = @TotalNA WHERE ExpID = NAExpID;
    
    SELECT 1;
END // 
DELIMITER ;

CALL AddNightAway(2, 3, 'TEST NA', 'Wiltshire', '2020-10-20', '2020-10-24');


DELIMITER //
CREATE PROCEDURE AddHike (
	IN HikeExpID		SMALLINT(2),
    IN Description		VARCHAR(50),
    IN Days				TINYINT(1),
    IN NADateStart		DATE,
    IN NADateEnd		DATE
)
BEGIN
	INSERT INTO exp1_exphikes (ExpID, Description, HikeDays, DateStart, DateEnd)
    VALUES (HikeExpID, Description, Days, NADateStart, NADateEnd);
    
    SELECT SUM(HikeDays) INTO @Hikes FROM exp1_exphikes WHERE ExpID = HikeExpID;
    
    UPDATE exp1_explorers SET TotalHikes = @Hikes WHERE ExpID = HikeExpID;
    
    SELECT 1;
END // 
DELIMITER ;

CALL AddHike(2, 'TEST HIKE', 5, '2020-10-20', '2020-10-24');




DELIMITER //
CREATE PROCEDURE GetAllExplorers (
	OUT ExpID			SMALLINT(2),
    OUT DisplayName		VARCHAR(250),
    OUT TypeDesc		VARCHAR(50),
    OUT StatusDesc		VARCHAR(20),
    OUT TotalNA			SMALLINT(2),
    OUT TotalHikes		SMALLINT(2),
    OUT ExpDateStart	DATE,
    OUT ExpDateEnd		DATE
)
BEGIN
	SELECT
		exp1_explorers.ExpID 			INTO @ExpID,
        edvr1_users.display_name 		INTO @DisplayName,
        exp1_exptypetypes.Description 	INTO @TypeDesc,
        exp1_expstatus.Description 		INTO @StatusDesc,
        exp1_explorers.TotalNightsAway 	INTO @TotalNA,
        exp1_explorers.TotalHikes 		INTO @TotalHikes,
        exp1_explorers.ExpDateStart 	INTO @ExpDateStart,
        exp1_explorers.ExpDateEnd 		INTO @ExpDateEnd
	FROM
        exp1_explorers E,
		edvr1_users U,
        exp1_expstatus S,
        exp1_exptypes T,
        exp1_exptypetypes Y
	WHERE
		U.ID = E.ExpWPID
        AND E.ExpStatusID = S.ExpStatusID
        AND E.Deleted = 0
        AND Y.ExpTypeTypeID = T.ExpTypeTypeID
		AND T.DateEnd IS NULL
        AND T.ExpID = E.ExpID
        ORDER BY U.display_name;
END // 
DELIMITER ;


DELIMITER //
CREATE PROCEDURE GetAllExplorers ()
BEGIN
	SELECT
		E.ExpID, 
        U.display_name,
        Y.Description AS TypeDesc,
        S.Description AS StatusDesc,
        E.TotalNightsAway,
        E.TotalHikes,
        E.ExpDateStart,
        E.ExpDateEnd
	FROM
		edvr1_users AS U,
        exp1_explorers AS E,
        exp1_expstatus AS S,
        exp1_exptypes AS T,
        exp1_exptypetypes AS Y
	WHERE
		U.ID = E.ExpWPID
        AND E.ExpStatusID = S.ExpStatusID
        AND E.Deleted = 0
        AND Y.ExpTypeTypeID = T.ExpTypeTypeID
		AND T.DateEnd IS NULL
        AND T.ExpID = E.ExpID
        ORDER BY U.display_name;
END // 
DELIMITER ;

DELIMITER //
CREATE PROCEDURE GetNonExplorerUsers()
BEGIN
	SELECT
		U.ID, U.display_name
	FROM
		edvr1_users AS U
	WHERE
		U.ID NOT IN (SELECT DISTINCT ExpWPID FROM exp1_explorers)
        ORDER BY U.display_name ASC;
END // 
DELIMITER ;

CALL GetAllExplorers();
DROP GetAllExplorers();

INSERT INTO `endvrwpdb1`.`exp1_badges` (`BadgeStatusID`, `BadgeTypeID`, `Name`, `Description`, `IconPath`, `DateStart`) VALUES ('1', '4', 'Activity - Activity Centre Service Badge', 'Activity - Activity Centre Service Badge', '/assets/badges/activity-activitycenterservice.jpg', '2010-01-01');
INSERT INTO `endvrwpdb1`.`exp1_badges` (`BadgeStatusID`, `BadgeTypeID`, `Name`, `Description`, `IconPath`, `DateStart`) VALUES ('1', '4', 'Activity - Athletics Badge', 'Activity - Athletics Badge', '/assets/badges/activity-athletics.jpg', '2010-01-01');
INSERT INTO `endvrwpdb1`.`exp1_badges` (`BadgeStatusID`, `BadgeTypeID`, `Name`, `Description`, `IconPath`, `DateStart`) VALUES ('1', '4', 'Activity - Camper Badge', 'Activity - Camper Badge', '/assets/badges/activity-camper.jpg', '2010-01-01');
INSERT INTO `endvrwpdb1`.`exp1_badges` (`BadgeStatusID`, `BadgeTypeID`, `Name`, `Description`, `IconPath`, `DateStart`) VALUES ('1', '4', 'Activity - Caving Badge', 'Activity - Caving Badge', '/assets/badges/activity-caving.jpg', '2010-01-01');
INSERT INTO `endvrwpdb1`.`exp1_badges` (`BadgeStatusID`, `BadgeTypeID`, `Name`, `Description`, `IconPath`, `DateStart`) VALUES ('1', '4', 'Activity - Chef Badge', 'Activity - Chef Badge', '/assets/badges/activity-chef.jpg', '2010-01-01');
INSERT INTO `endvrwpdb1`.`exp1_badges` (`BadgeStatusID`, `BadgeTypeID`, `Name`, `Description`, `IconPath`, `DateStart`) VALUES ('1', '4', 'Activity - Climber Badge', 'Activity - Climber Badge', '/assets/badges/activity-climber.jpg', '2010-01-01');
INSERT INTO `endvrwpdb1`.`exp1_badges` (`BadgeStatusID`, `BadgeTypeID`, `Name`, `Description`, `IconPath`, `DateStart`) VALUES ('1', '4', 'Activity - Creative Arts Badge', 'Activity - Creative Arts Badge', '/assets/badges/activity-creativearts.jpg', '2010-01-01');
INSERT INTO `endvrwpdb1`.`exp1_badges` (`BadgeStatusID`, `BadgeTypeID`, `Name`, `Description`, `IconPath`, `DateStart`) VALUES ('1', '4', 'Activity - Fundraising Badge', 'Activity - Fundraising Badge', '/assets/badges/activity-fundraising.jpg', '2010-01-01');
INSERT INTO `endvrwpdb1`.`exp1_badges` (`BadgeStatusID`, `BadgeTypeID`, `Name`, `Description`, `IconPath`, `DateStart`) VALUES ('1', '4', 'Activity - Global Issues Badge', 'Activity - Global Issues Badge', '/assets/badges/activity-globalissues.jpg', '2010-01-01');
INSERT INTO `endvrwpdb1`.`exp1_badges` (`BadgeStatusID`, `BadgeTypeID`, `Name`, `Description`, `IconPath`, `DateStart`) VALUES ('1', '4', 'Activity - Hill Walker Badge', 'Activity - Hill Walker  Badge', '/assets/badges/activity-hillwalker.jpg', '2010-01-01');

INSERT INTO `endvrwpdb1`.`exp1_badges` (`BadgeStatusID`, `BadgeTypeID`, `Name`, `Description`, `IconPath`, `DateStart`) VALUES ('1', '4', 'Activity - Instructor Badge', 'Activity - Instructor Badge', '/assets/badges/activity-instructor.jpg', '2010-01-01');
INSERT INTO `endvrwpdb1`.`exp1_badges` (`BadgeStatusID`, `BadgeTypeID`, `Name`, `Description`, `IconPath`, `DateStart`) VALUES ('1', '4', 'Activity - Plus Badge', 'Activity - Plus Badge', '/assets/badges/activity-instructor.jpg', '2010-01-01');
INSERT INTO `endvrwpdb1`.`exp1_badges` (`BadgeStatusID`, `BadgeTypeID`, `Name`, `Description`, `IconPath`, `DateStart`) VALUES ('1', '4', 'Activity - International Badge', 'Activity - International Badge', '/assets/badges/activity-international.jpg', '2010-01-01');
INSERT INTO `endvrwpdb1`.`exp1_badges` (`BadgeStatusID`, `BadgeTypeID`, `Name`, `Description`, `IconPath`, `DateStart`) VALUES ('1', '4', 'Activity - Leadership Badge', 'Activity - Leadership Badge', '/assets/badges/activity-leadership.jpg', '2010-01-01');
INSERT INTO `endvrwpdb1`.`exp1_badges` (`BadgeStatusID`, `BadgeTypeID`, `Name`, `Description`, `IconPath`, `DateStart`) VALUES ('1', '4', 'Activity - Live Saver  Badge', 'Activity - Live Saver  Badge', '/assets/badges/activity-lifesaver.jpg', '2010-01-01');
INSERT INTO `endvrwpdb1`.`exp1_badges` (`BadgeStatusID`, `BadgeTypeID`, `Name`, `Description`, `IconPath`, `DateStart`) VALUES ('1', '4', 'Activity - Media Relations Badge', 'Activity - Media Relations Badge', '/assets/badges/activity-mediarelations.jpg', '2010-01-01');
INSERT INTO `endvrwpdb1`.`exp1_badges` (`BadgeStatusID`, `BadgeTypeID`, `Name`, `Description`, `IconPath`, `DateStart`) VALUES ('1', '4', 'Activity - Motor Sports Badge', 'Activity - Motor Sports Badge', '/assets/badges/activity-motorsports.jpg', '2010-01-01');
INSERT INTO `endvrwpdb1`.`exp1_badges` (`BadgeStatusID`, `BadgeTypeID`, `Name`, `Description`, `IconPath`, `DateStart`) VALUES ('1', '4', 'Activity - Mountain Biking Badge', 'Activity - Mountain Biking Badge', '/assets/badges/activity-mountainbiking.jpg', '2010-01-01');
INSERT INTO `endvrwpdb1`.`exp1_badges` (`BadgeStatusID`, `BadgeTypeID`, `Name`, `Description`, `IconPath`, `DateStart`) VALUES ('1', '4', 'Activity - Naturalist Badge', 'Activity - Naturalist Badge', '/assets/badges/activity-naturalist.jpg', '2010-01-01');
INSERT INTO `endvrwpdb1`.`exp1_badges` (`BadgeStatusID`, `BadgeTypeID`, `Name`, `Description`, `IconPath`, `DateStart`) VALUES ('1', '4', 'Activity - Performing Arts Badge', 'Activity - PErforming Arts Badge', '/assets/badges/activity-performingarts.jpg', '2010-01-01');
INSERT INTO `endvrwpdb1`.`exp1_badges` (`BadgeStatusID`, `BadgeTypeID`, `Name`, `Description`, `IconPath`, `DateStart`) VALUES ('1', '4', 'Activity - Physical Recreation Badge', 'Activity - Physical Recreation Badge', '/assets/badges/activity-physicalrecreation.jpg', '2010-01-01');
INSERT INTO `endvrwpdb1`.`exp1_badges` (`BadgeStatusID`, `BadgeTypeID`, `Name`, `Description`, `IconPath`, `DateStart`) VALUES ('1', '4', 'Activity - Pioneer Badge', 'Activity - Pioneer Badge', '/assets/badges/activity-pioneer.jpg', '2010-01-01');
INSERT INTO `endvrwpdb1`.`exp1_badges` (`BadgeStatusID`, `BadgeTypeID`, `Name`, `Description`, `IconPath`, `DateStart`) VALUES ('1', '4', 'Activity - Racket Sports Badge', 'Activity - Racket Sports Badge', '/assets/badges/activity-racketsports.jpg', '2010-01-01');
INSERT INTO `endvrwpdb1`.`exp1_badges` (`BadgeStatusID`, `BadgeTypeID`, `Name`, `Description`, `IconPath`, `DateStart`) VALUES ('1', '4', 'Activity - Science and Technology Badge', 'Activity - Science and Technology Badge', '/assets/badges/activity-scienceandtechnology.jpg', '2010-01-01');
INSERT INTO `endvrwpdb1`.`exp1_badges` (`BadgeStatusID`, `BadgeTypeID`, `Name`, `Description`, `IconPath`, `DateStart`) VALUES ('1', '4', 'Activity - Skiing Badge', 'Activity - Skiing Badge', '/assets/badges/activity-skiing.jpg', '2010-01-01');
INSERT INTO `endvrwpdb1`.`exp1_badges` (`BadgeStatusID`, `BadgeTypeID`, `Name`, `Description`, `IconPath`, `DateStart`) VALUES ('1', '4', 'Activity - Snowboarding Badge', 'Activity - Snowboarding Badge', '/assets/badges/activity-snowboarding.jpg', '2010-01-01');
INSERT INTO `endvrwpdb1`.`exp1_badges` (`BadgeStatusID`, `BadgeTypeID`, `Name`, `Description`, `IconPath`, `DateStart`) VALUES ('1', '4', 'Activity - Street Sports Badge', 'Activity - Street Sports Badge', '/assets/badges/activity-streetsports.jpg', '2010-01-01');
INSERT INTO `endvrwpdb1`.`exp1_badges` (`BadgeStatusID`, `BadgeTypeID`, `Name`, `Description`, `IconPath`, `DateStart`) VALUES ('1', '4', 'Activity - Survival Skills Badge', 'Activity - Survival Skills Badge', '/assets/badges/activity-survivalskills.jpg', '2010-01-01');
INSERT INTO `endvrwpdb1`.`exp1_badges` (`BadgeStatusID`, `BadgeTypeID`, `Name`, `Description`, `IconPath`, `DateStart`) VALUES ('1', '4', 'Activity - Water Activities Badge', 'Activity - Water Activities Badge', '/assets/badges/activity-wateractivities.jpg', '2010-01-01');

SET FOREIGN_KEY_CHECKS=0;
TRUNCATE `endvrwpdb1`.`exp1_explorers`;
TRUNCATE `endvrwpdb1`.`exp1_exptypes`;
TRUNCATE `endvrwpdb1`.`exp1_exphikes`;
TRUNCATE `endvrwpdb1`.`exp1_expna`;
TRUNCATE `endvrwpdb1`.`exp1_expbadges`;
SET FOREIGN_KEY_CHECKS=1;


INSERT INTO `endvrwpdb1`.`exp1_exproletypes` (`Description`) VALUES ('Explorer');
INSERT INTO `endvrwpdb1`.`exp1_exproletypes` (`Description`) VALUES ('Assitant Patrol Leader');
INSERT INTO `endvrwpdb1`.`exp1_exproletypes` (`Description`) VALUES ('Patrol Leader');

SET FOREIGN_KEY_CHECKS=0;
TRUNCATE `endvrwpdb1`.`exp1_expbadgereqts`;
TRUNCATE `endvrwpdb1`.`exp1_expbadges`;
SET FOREIGN_KEY_CHECKS=1;
INSERT INTO `endvrwpdb1`.`exp1_expstatus` (`Description`) VALUES ('Active');
INSERT INTO `endvrwpdb1`.`exp1_expstatus` (`Description`) VALUES ('Inactive');


use endvrwpdb1;
SELECT SUM(NADays) FROM exp1_expna WHERE ExpID = 14
UPDATE exp1_explorers SET TotalNightsAway = 89 WHERE ExpID = 14

SET FOREIGN_KEY_CHECKS=0;
TRUNCATE `endvrwpdb1`.`exp1_badgereqts`;
SET FOREIGN_KEY_CHECKS=1;

SELECT B.BadgeID , B.Name, B.Description, S.Description, T.Description
FROM exp1_badges B, exp1_badgestatus S, exp1_badgetypes T
WHERE T.BadgeTypeID = B.BadgeTypeID AND S.BadgeStatusID = B.BadgeStatusID
ORDER BY T.Description, B.Description

SELECT B.BadgeID , CONCAT(B.Name, ' (', S.Description, ', ', T.Description, ')'), B.Description
FROM exp1_badges B, exp1_badgestatus S, exp1_badgetypes T
WHERE T.BadgeTypeID = B.BadgeTypeID AND S.BadgeStatusID = B.BadgeStatusID
ORDER BY T.Description, B.Description

use endvrwpdb1;
;; INSERT INTO exp1_explorers (ExpWPID, ExpDateStart, ExpStatusID) VALUES (1, CURRENT_DATE, 1);

SELECT B.BadgeID, B.Name, B.IconPath, B.Description, S.Description, T.Description, E.ExpBadgeID, E.DateStart, E.DateEnd
FROM exp1_badges B, exp1_badgestatus S, exp1_badgetypes T, exp1_expbadges E 
WHERE T.BadgeTypeID = B.BadgeTypeID AND S.BadgeStatusID = B.BadgeStatusID AND B.BadgeID = E.BadgeID AND E.ExpID = 1
GROUP BY B.BadgeTypeID ORDER BY B.Description;

 SELECT E.ExpID, U.display_name, Y.Description, S.Description, E.TotalNightsAway, E.TotalHikes, E.ExpDateStart, E.ExpDateEnd FROM edvr1_users U, exp1_explorers E, exp1_expstatus S, exp1_exptypes T, exp1_exptypetypes Y WHERE U.ID = E.ExpWPID AND E.Deleted = 0 AND E.ExpStatusID = S.ExpStatusID AND T.ExpID = E.ExpID AND Y.ExpTypeTypeID = T.ExpTypeTypeID AND T.DateEnd IS NULL ORDER BY U.display_name; 


SELECT U.display_name, S.Description, E.ExpDateStart, E.ExpDateEnd, E.ExpID, E.TotalNightsAway, E.TotalHikes, Y.Description
FROM edvr1_users U, exp1_explorers E, exp1_expstatus S, exp1_exptypes T, exp1_exptypetypes Y
WHERE U.ID = E.ExpWPID AND E.ExpStatusID = S.ExpStatusID AND E.Deleted = 0
AND Y.ExpTypeTypeID = T.ExpTypeTypeID AND T.ExpID = E.ExpID AND T.DateEnd IS NULL 
ORDER BY U.display_name;