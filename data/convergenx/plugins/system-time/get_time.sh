#!/bin/sh
echo "🕐 $(date '+%H:%M:%S')"
echo "📅 $(date '+%A, %B %d, %Y')"
echo "⏰ Uptime: $(uptime | sed 's/.*up *//; s/,.*//')"
