-- command : search
-- return : Student
-- This is a comment, a comment, a comment 
-- and a comment, a comment, a comment
Select * FROM students

-- command : searchByName
Select * FROM students WHERE name={{name}} AND name IS NOT NULL

-- command : testPlaceHolder
SELECT * FROM {{#table}} WHERE 1=1

-- command : testForeach
SELECT * FROM students WHERE 1=1
{{foreach conditions as key => value}}
AND {{#key}} = {{value}}
{{endforeach}}
ORDER BY id DESC

-- command : testIf
SELECT * FROM students WHERE 1=1
{{if age}} AND age > {{age}}{{endif}}
{{if name = 'michael'}} AND name = 'michael'{{endif}} 
{{if class > maxClass}} AND age = 'MAX_CLASS'{{endif}}
{{if class > 10}} AND age > 10{{endif}}

-- command : testStartByToken
{{#value}}{{if value}} 1{{endif}}

-- command : testMixedUp
SELECT * FROM students
WHERE 1=1
{{foreach conditions as key => value}}
  {{if value}}
    AND {{#key}} = {{value}}
  {{endif}}
{{endforeach}}
ORDER BY id DESC

-- command : SerachByXXXX
Select * 
FROM BS_STUDETNS 
WHERE name={{name}}
  AND age > {{ageMin}}
  AND class = {{class}}
ORDER BY age DESC

Select * 
FROM BS_STUDETNS 
WHERE name={{name}}
  AND age > {{ageMin}}
  AND class = {{class}}
ORDER BY age DESC