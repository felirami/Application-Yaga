<?php if(!defined('APPLICATION')) exit();
/* Copyright 2013 Zachary Doll */

/**
 * Contains render functions that can be used cross controller
 */
/**
 * Renders a list of available actions that also contains the current count of
 * reactions an item has received
 *
 * @param int $ID
 * @param string $Type 'discussion', 'activity', or 'comment'
 * @param bool $Echo Should it be echoed?
 * @return mixed String if $Echo is false, TRUE otherwise
 */
if(!function_exists('RenderReactions')) {

  function RenderReactions($ID, $Type, $Echo = TRUE) {
    $Reactions = Yaga::ReactionModel()->GetAllReactions($ID, $Type);
    $ActionsString = '';
    foreach($Reactions as $Action) {
      if(CheckPermission($Action->Permission)) {
        $ActionsString .= Anchor(
                Wrap('&nbsp;', 'span', array('class' => 'ReactSprite React-' . $Action->ActionID . ' ' . $Action->CssClass)) .
                WrapIf(count($Action->UserIDs), 'span', array('class' => 'Count')) .
                Wrap($Action->Name, 'span', array('class' => 'ReactLabel')), 'react/' . $Type . '/' . $ID . '/' . $Action->ActionID, 'Hijack ReactButton'
        );
      }
    }

    $AllActionsString = Wrap($ActionsString, 'span', array('class' => 'ReactMenu'));

    if($Echo) {
      echo $AllActionsString;
      return true;
    }
    else {
      return $AllActionsString;
    }
  }

}

/**
 * Renders the reaction record for a specific item
 * @param int $ID
 * @param string $Type 'discussion', 'activity', or 'comment'
 */
if(!function_exists('RenderReactionRecord')) {

  function RenderReactionRecord($ID, $Type) {
    $Reactions = Yaga::ReactionModel()->GetAllReactions($ID, $Type);
    foreach($Reactions as $Reaction) {
      if($Reaction->UserIDs) {
        foreach($Reaction->UserIDs as $Index => $UserID) {
          $User = Gdn::UserModel()->GetID($UserID);
          $String = UserPhoto($User, array('Size' => 'Small'));
          $String .= '<span class="ReactSprite Reaction-' . $Reaction->ActionID . ' ' . $Reaction->CssClass . '"></span>';
          $Wrapttributes = array(
              'class' => 'UserReactionWrap',
              'data-userid' => $User->UserID,
              'title' => $User->Name . ' - ' . $Reaction->Name . ' on ' . Gdn_Format::Date($Reaction->Dates[$Index], '%B %e, %Y')
          );
          echo Wrap($String, 'span', $Wrapttributes);
        }
      }
    }
  }

}

if(!function_exists('ActionRow')) {
  function ActionRow($Action) {
    return Wrap(
            Wrap(
                    Anchor(T('Edit'), 'yaga/action/edit/' . $Action->ActionID, array('class' => 'Popup SmallButton')) . Anchor(T('Delete'), 'yaga/action/delete/' . $Action->ActionID, array('class' => 'Popup SmallButton')), 'div', array('class' => 'Tools')) .
            Wrap(
                    Wrap($Action->Name, 'h4') .
                    Wrap(
                            Wrap($Action->Description, 'span') . ' ' .
                            Wrap(Plural($Action->AwardValue, '%s Point', '%s Points'), 'span'), 'div', array('class' => 'Meta')) .
                    Wrap(
                            Wrap('&nbsp;', 'span', array('class' => 'ReactSprite React-' . $Action->ActionID . ' ' . $Action->CssClass)) .
                            WrapIf(rand(0, 18), 'span', array('class' => 'Count')) .
                            Wrap($Action->Name, 'span', array('class' => 'ReactLabel')), 'div', array('class' => 'Preview Reactions')), 'div', array('class' => 'Action')), 'li', array('id' => 'ActionID_' . $Action->ActionID));
  }
}