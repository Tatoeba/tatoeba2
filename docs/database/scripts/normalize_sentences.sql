Delimiter | 

DROP PROCEDURE normalize_sentences |

CREATE PROCEDURE normalize_sentences()

BEGIN
    
    -- Adding adequate spaces before ?!;:
    UPDATE sentences SET text = replace(text,'?', ' ?') where text like '%\?%' AND lang = 'fra';
    UPDATE sentences SET text = replace(text,'!', ' !') where text like '%!%' AND lang = 'fra';
    UPDATE sentences SET text = replace(text,';', ' ;') where text like '%;%' AND lang = 'fra';
    UPDATE sentences SET text = replace(text,':', ' :') where text like '%:%' AND lang = 'fra';
    
    -- Remove double spaces
    UPDATE sentences SET text = replace(text,'  ', ' ') where text like '%  %';
    UPDATE sentences SET text = replace(text,'  ', ' ') where text like '%  %';
    UPDATE sentences SET text = replace(text,'  ', ' ') where text like '%  %';
    
    -- Remove new lines and tabs
    UPDATE sentences SET text = replace(text,'\t', '') where text like '%\t%';
    UPDATE sentences SET text = replace(text,'\n', '') where text like '%\n%';
    UPDATE sentences SET text = replace(text,'\r', '') where text like '%\r%';
    
END |