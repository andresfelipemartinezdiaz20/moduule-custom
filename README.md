# User Fields Configuration and Record Management Guide in Drupal

## Step 1: Access User Fields Configuration
1. Log in to your Drupal administration panel.
2. Go to "Configuration" in the top administration menu.
3. In the "People" section, select "Account settings."
4. Click on "Manage fields." This will take you to the page where you can manage user profile fields.

## Step 2: Add the "birth date" Field
1. On the "Manage fields" page, click on "Add field."
2. In "Add a new field," select "Date" as the field type.
3. In the "Label" field, enter "Birth Date."
4. In the "Field name" field, Drupal will suggest something like "field_birth_date." You can change it if you prefer.
5. Click "Save and continue."
6. On the next screen, adjust the field settings:
   - **Date type:** Select "Date" (date only), as you don't need the time.
   - Leave the other settings as they are or adjust them according to your needs.
7. Click "Save field settings."
8. Configure the display and behavior of this field on user screens as needed. You can adjust how the date will be shown and in what format.

## Step 3: Add the "DNI" Field (Integer)
1. Go back to the "Manage fields" page.
2. Click on "Add field" again.
3. This time, select "Integer" as the field type. This ensures that the field only accepts integer numbers (like a DNI).
4. In the "Label" field, enter "DNI."
5. In the "Field name" field, Drupal will suggest something like "field_dni." You can modify it if you wish.
6. Click "Save and continue."
7. On the next screen, configure the field:
   - **Number type:** Leave it as "Integer."
   - **Maximum length:** Adjust the maximum length according to the DNI format (e.g., if the DNI has 8 digits, set it to a maximum of 8).
8. Click "Save field settings."
9. Configure the field display as needed (e.g., if you want to show the DNI with a special format or a help message).

## Step 4: Install and Enable a Custom Module
1. Access your Drupal installation folder.
2. Check if the "custom" folder exists under "modules." If not, create it.
3. Extract the module file into the "custom" folder.
4. Enable the module from the Drupal administration interface or use Drush with the following command:  
   `drush en user_custom -y`
5. Verify that the module is enabled correctly.

## URLs to Manage Records and Configurations

- To manage user registration records:  
  `/user-registry/form`
  
- To view the submitted records:  
  `/admin/registry/users`
  
- To manage form titles:  
  `/admin/config/user-registry/settings`
  
- To query a registered user by their DNI:  
  `/api/user/{dni}`
