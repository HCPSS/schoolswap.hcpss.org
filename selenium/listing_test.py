from selenium.webdriver.remote.webelement import WebElement

from base_test_case import BaseTestCase


class ListingTest(BaseTestCase):
    def test_listing(self):
        self.allow_all_logins()

        self.login("admin")
        self.open("http://drupal/admin/structure/taxonomy/manage/category/add")
        self.type("#edit-name-0-value", "Spaceships")
        self.click("#edit-submit")
        self.assert_text("Created new term Spaceships")
        self.logout()

        self.login("sderkins")
        self.open("http://drupal/node/add/listing")
        self.type("#edit-title-0-value", "My Spaceship")
        self.type(".ck-content", "It's been in a few crashes but still works.")
        self.type("#edit-field-pictures-0-upload", "/app/spiff.jpg")
        self.click(".rotate-icon")
        self.assert_attribute(".rotate-icon", "data-rotate", "90")
        self.click("#edit-field-categories-1")
        self.click("#edit-submit")
        self.logout()

        self.open("http://drupal")
        self.assert_text("It's been in a few crashes but still works.")

        self.login("btracer")
        self.open("http://drupal")
        self.click_link("Request")
        self.click("#edit-checkout")
        self.type("#edit-school", "Office of space travel")
        self.type("#edit-name", "Bullet Tracer")
        self.type("#edit-date", "11/08/2026")
        self.click("#edit-submit")
        self.assert_text("We'll get you that stuff soon!")
        self.logout()

        self.open("http://mailhog:8025")
        self.assert_text("Pick-up request submitted");
