-- command : search
-- return : Student
-- This is a comment, a comment, a comment 
-- and a comment, a comment, a comment
Select * FROM BS_Students

-- command : searchByName
Select * FROM BS_STUDETNS WHERE name={{name}}

-- command : SearchByNameXXX
Select * 
FROM BS_STUDETNS 
WHERE name={{name}}
  AND age > {{ageMin}}
  AND class = {{class}}
ORDER BY age DESC

Select * 
FROM BS_STUDETNS 
WHERE name={{name}}
  {{if (isset($name))}}AND age > {{ageMin}}{{endif}}
  AND class = {{class}}
ORDER BY age DESC

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