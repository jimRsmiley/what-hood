module Whathood

    class Util

        def self.exec_sql_stmt(sql_stmt,db_name)
            puts "executing #{sql_stmt}"
            puts `sudo -u postgres psql --tuples-only -c "#{sql_stmt}" #{db_name}`
        end
    end
end
