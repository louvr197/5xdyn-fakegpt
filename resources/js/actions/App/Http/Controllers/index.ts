import AskController from './AskController'
import AskStreamController from './AskStreamController'
import ConversationController from './ConversationController'
import MessageController from './MessageController'
import UserModelController from './UserModelController'
import CustomInstructionsController from './CustomInstructionsController'
import PresetController from './PresetController'
import SitemapController from './SitemapController'
import Settings from './Settings'
const Controllers = {
    AskController: Object.assign(AskController, AskController),
AskStreamController: Object.assign(AskStreamController, AskStreamController),
ConversationController: Object.assign(ConversationController, ConversationController),
MessageController: Object.assign(MessageController, MessageController),
UserModelController: Object.assign(UserModelController, UserModelController),
CustomInstructionsController: Object.assign(CustomInstructionsController, CustomInstructionsController),
PresetController: Object.assign(PresetController, PresetController),
SitemapController: Object.assign(SitemapController, SitemapController),
Settings: Object.assign(Settings, Settings),
}

export default Controllers