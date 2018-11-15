// create view of each class type & its aggregated count
CREATE VIEW class_count_view AS
SELECT ct.Description, ct.HrRate, count
FROM classtype ct
JOIN (SELECT count(*) as count, c.ClassTypeID FROM class c GROUP BY c.ClassTypeID) temp
ON ct.ClassTypeID = temp.ClassTypeID;
