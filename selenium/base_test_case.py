from selenium.webdriver.remote.webelement import WebElement
from seleniumbase import BaseCase

class BaseTestCase(BaseCase):
    def is_logged_in(self):
        return self.is_element_visible('#toolbar-item-user')
    def login(self, role="admin", user=None):
        username = role if not user else user.username
        password = role if not user else user.password

        self.open("http://drupal/user/login")
        self.type('#edit-name', username)
        self.type('#edit-pass', password)
        self.click('#edit-submit')
    def login_as_super(self):
        self.open("http://drupal/user/login")
        self.type('#edit-name', 'admin')
        self.type('#edit-pass', 'admin')
        self.click('#edit-submit')
    def logout(self):
        self.open("http://drupal/user/logout")
    def assert_no_content(self, logout=True):
        self.open("http://drupal")

        if (not self.is_logged_in()):
            self.login_as_admin()

        self.open("http://drupal/admin/content")
        self.assert_text('No content available.')

        if (logout):
            self.logout()
    def allow_all_logins(self):
        if self.is_logged_in():
            self.logout()
        self.login()

        self.open('http://drupal/admin/config/people/simplesamlphp_auth/local')
        elements = self.find_elements('#edit-allow-default-login-roles input')
        for element in elements:
            if not element.is_selected():
                element.click()

        self.click('#edit-submit')
        self.logout()

    def delete_content(self):
        if (not self.is_logged_in()):
            self.login_as_admin()

        self.open("http://drupal/admin/content")

        if (not self.is_text_visible('No content available.')):
            self.select_option_by_text("#edit-action", "Delete content")
            self.check_if_unchecked('[title="Select all rows in this table"]')
            self.click("#edit-submit")

            # Confirm
            self.click("#edit-submit")

        self.assert_text('No content available.')
