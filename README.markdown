    {exp:timetravel by='day' channel='default_site'}
      {oldest}<a href="{path='plugins/timetravel'}">&laquo;Oldest</a>{/oldest} 
      {older}<a href="{path='plugins/timetravel'}">&lsaquo;Older</a>{/older} 
      <strong>{current format='%F %j%S, %Y'}</strong>
      {newer}<a href="{path='plugins/timetravel'}">Newer&rsaquo;</a>{/newer} 
      {newest}<a href="{path='plugins/timetravel'}">Newest&raquo;</a>{/newest}
    {/exp:timetravel}

Parameters
* by  : day|month|year
* author_id : Member id
* category  : Category id
* category_group  : Category Group id
* channel : Channel short name
* entry_id_from : Entry id
* entry_id_to : Entry id
* group_id  : Member group id
* show_expired  : yes|no
* show_future_entries : yes|no
* status  : Status
* start_on  : Date (%Y-%m-%d %H:%i)
* stop_before : Date (%Y-%m-%d %H:%i)
* uncategorized_entries : yes|no
* username  : Member username


If you are using Timetravel to walk through years, you need to add year='{segment_n}' and dynamic='off' to your channel:entries tag.