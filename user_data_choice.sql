create view user_data_choice as
select (select count(distinct user_id) from settingchange) as changers,
(select count(distinct id) from user where id not in (select distinct user_id from settingchange) and isAdmin != 1)