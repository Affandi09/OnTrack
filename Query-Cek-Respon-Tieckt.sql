select ticket,tickets_departments.name As Department,clients.name As Client,assets.name As Asset,projects.name as Project,
(select name from people where tickets.userid = people.id) As Submiter,
(select name from people where tickets.adminid = people.id ) As PIC,
subject,tickets.notes,timestamp as DateAdded,respondtime As Respond,inprogresstime as Progress,closetime As Close,status from tickets
inner join tickets_departments on departmentid = tickets_departments.id
left outer join clients on clientid = clients.id
left outer join assets on assetid = assets.id
left outer join projects on projectid =  projects.id
where timestamp between '2025-12-01 00:00:00' and '2025-12-31 00:00:00'