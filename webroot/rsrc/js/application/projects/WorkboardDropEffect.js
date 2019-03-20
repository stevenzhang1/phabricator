/**
 * @provides javelin-workboard-drop-effect
 * @requires javelin-install
 *           javelin-dom
 * @javelin
 */

JX.install('WorkboardDropEffect', {

  properties: {
    icon: null,
    color: null,
    content: null,
    conditions: []
  },

  statics: {
    newFromDictionary: function(map) {
      return new JX.WorkboardDropEffect()
        .setIcon(map.icon)
        .setColor(map.color)
        .setContent(JX.$H(map.content))
        .setConditions(map.conditions || []);
    }
  },

  members: {
    newNode: function() {
      var icon = new JX.PHUIXIconView()
        .setIcon(this.getIcon())
        .setColor(this.getColor())
        .getNode();

      return JX.$N('li', {}, [icon, this.getContent()]);
    },

    isEffectVisibleForCard: function(card) {
      var conditions = this.getConditions();

      var properties = card.getProperties();
      for (var ii = 0; ii < conditions.length; ii++) {
        var condition = conditions[ii];

        var field = properties[condition.field];
        var value = condition.value;

        var result = true;
        switch (condition.operator) {
          case '!=':
            result = (field !== value);
            break;
        }

        if (!result) {
          return false;
        }
      }

      return true;
    }

  }
});
