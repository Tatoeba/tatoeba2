alter table transcriptions ADD needsReview tinyint(1) NOT NULL DEFAULT '1' after user_id;
update transcriptions set needsReview = 0 where user_id is not null;
update transcriptions t left join sentences s on s.id = t.sentence_id set t.needsReview = 0 where s.lang in ('cmn', 'uzb');
update transcriptions t join sentences s on s.id = t.sentence_id and s.text = t.text set needsReview = 0 where s.lang = 'jpn';
