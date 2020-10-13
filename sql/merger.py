
import sys, sqlite3

class sqlMerge(object):
    """Basic python script to merge data of 2 !!!IDENTICAL!!!! SQL tables"""

    def __init__(self, parent=None):
        super(sqlMerge, self).__init__()

        self.db_a = None
        self.db_b = None

    def loadTables(self, file_a, file_b):
        self.db_a = sqlite3.connect(file_a)
        self.db_b = sqlite3.connect(file_b)

        cursor_a = self.db_a.cursor()
        cursor_a.execute("SELECT name FROM sqlite_master WHERE type='table';")

        table_to_merge = []
        table_counter = 0
        print("SQL Tables available: \n===================================================\n")
        for table_item in cursor_a.fetchall():
            if table_item[0] == "registered_users":
                continue
            table_to_merge.append( table_item[0])
            print("-> " + table_to_merge[-1])
        print("\n===================================================\n")

        return table_to_merge

    def merge(self, table_names):
        cursor_a = self.db_a.cursor()
        cursor_b = self.db_b.cursor()

        try:
            for table_name in table_names:
                new_table_name = table_name + "_new"

                cursor_b.execute("CREATE TABLE IF NOT EXISTS " + table_name# + " AS SELECT * FROM " + table_name)
                for row in cursor_a.execute("SELECT * FROM " + table_name):
                    print(row)
                    cursor_b.execute("INSERT INTO " + new_table_name + " VALUES" + str(row) +";")

                cursor_b.execute("DROP TABLE IF EXISTS " + table_name);
                cursor_b.execute("ALTER TABLE " + new_table_name + " RENAME TO " + table_name);
                self.db_b.commit()

        except sqlite3.OperationalError:
            import traceback
            print (traceback.format_exc())
            print("ERROR!: Merge Failed")
            cursor_a.execute("DROP TABLE IF EXISTS " + new_table_name);

        finally:
            self.db_a.close()
            self.db_b.close()
            print("\n\nMerge Successful!\n")
            
        return

    def main(self):
        print("Please enter name of db file")
        file_name_a = input("File Name A:")
        file_name_b = input("File Name B:")

        self.merge(self.loadTables(file_name_a, file_name_b))

        return

if __name__ == '__main__':
    app = sqlMerge()
    app.main()
