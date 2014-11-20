## Usage

    {exp:timetravel by='day' channel='default_site'}
      {oldest}<a href="{path='group/template'}">Oldest</a>{/oldest}
      {older}<a href="{path='group/template'}">Older</a>{/older}
      <strong>{current format='%F %j%S, %Y'}</strong>
      {newer}<a href="{path='group/template'}">Newer</a>{/newer}
      {newest}<a href="{path='group/template'}">Newest</a>{/newest}
    {/exp:timetravel}

    {exp:channel:entries channel='default_site'}
        // Your entry data
    {/exp:channel:entries}

### Parameters

 * by  = day|month|year
 * author_id = Member id
 * category  = Category id
 * category_group  = Category Group id
 * channel = Channel short name
 * entry_id_from = Entry id
 * entry_id_to = Entry id
 * group_id  = Member group id
 * show_expired  = yes|no
 * show_future_entries = yes|no
 * status  = Status
 * start_on  = Date (%Y-%m-%d %H:%i)
 * stop_before = Date (%Y-%m-%d %H:%i)
 * uncategorized_entries = yes|no
 * username  = Member username

## Note

If you are using Timetravel to navigate through years, you need to add year='{segment_n}' and dynamic='off' to your channel:entries tag.

    {exp:channel:entries channel='default_site' dynamic='off' year='{segment_n}'}
        // Your entry data
    {/exp:channel:entries}